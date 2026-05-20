import { useState, useEffect, type FormEvent } from 'react'
import { useAuth } from '../../context/AuthContext'
import { API_ROUTES } from '../../config/apiRoutes.js'
import { useCrud, apiCall } from './hooks/useCrud'
import { CrudPage } from './components/CrudPage'
import type { Column } from './components/CrudPage'
import { FormModal, Field, inputCls } from './components/FormModal'
import type { Amigo, User } from '../../types'

const COLUMNS: Column<Amigo>[] = [
  { header: 'ID', key: 'id', width: 'w-12' },
  {
    header: 'Usuario',
    key: 'user_id',
    render: (row) => row.user?.name ?? String(row.user_id),
  },
  {
    header: 'Amigo',
    key: 'friend_id',
    render: (row) => row.friend?.name ?? String(row.friend_id),
  },
  {
    header: 'Estado',
    key: 'receiver_id',
    render: (row) =>
      row.receiver_id === null ? (
        <span className="text-green-400 text-xs font-medium">Confirmado</span>
      ) : (
        <span className="text-yellow-400 text-xs font-medium">Pendiente</span>
      ),
  },
]

interface FormState {
  user_id: string
  friend_id: string
  receiver_id: string
}

const EMPTY: FormState = { user_id: '', friend_id: '', receiver_id: '' }

export function AdminAmigos() {
  const { token } = useAuth()
  const crud = useCrud<Amigo>({ listUrl: API_ROUTES.ADMIN.AMIGOS.LIST })

  const [users, setUsers] = useState<User[]>([])
  const [modal, setModal] = useState<{ open: boolean; editing: Amigo | null }>({ open: false, editing: null })
  const [form, setForm] = useState<FormState>(EMPTY)
  const [submitting, setSubmitting] = useState(false)
  const [formError, setFormError] = useState<string | null>(null)
  const [deleteId, setDeleteId] = useState<number | null>(null)

  useEffect(() => {
    fetch(API_ROUTES.ADMIN.USERS.LIST, { headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' } })
      .then((r) => r.json())
      .then((j) => {
        const items = Array.isArray(j.data) ? j.data : j.data?.data ?? []
        setUsers(items)
      })
      .catch(() => {})
  }, [token])

  function openNew() {
    setForm(EMPTY)
    setFormError(null)
    setModal({ open: true, editing: null })
  }

  function openEdit(row: Amigo) {
    setForm({
      user_id: String(row.user_id),
      friend_id: String(row.friend_id),
      receiver_id: row.receiver_id !== null ? String(row.receiver_id) : '',
    })
    setFormError(null)
    setModal({ open: true, editing: row })
  }

  function close() {
    setModal({ open: false, editing: null })
  }

  function set(key: keyof FormState, val: string) {
    setForm((f) => ({ ...f, [key]: val }))
  }

  async function handleSubmit(e: FormEvent) {
    e.preventDefault()
    setSubmitting(true)
    setFormError(null)

    const body: Record<string, unknown> = {
      user_id: Number(form.user_id),
      friend_id: Number(form.friend_id),
      receiver_id: form.receiver_id ? Number(form.receiver_id) : null,
    }

    const { editing } = modal
    const url = editing ? API_ROUTES.ADMIN.AMIGOS.UPDATE(editing.id) : API_ROUTES.ADMIN.AMIGOS.CREATE
    const method = editing ? 'PUT' : 'POST'

    const res = await apiCall(url, method, token, body)
    setSubmitting(false)

    if (!res.ok) { setFormError(res.message); return }
    close()
    crud.refresh()
  }

  async function handleDeleteConfirm(row: Amigo) {
    await apiCall(API_ROUTES.ADMIN.AMIGOS.DELETE(row.id), 'DELETE', token)
    setDeleteId(null)
    crud.refresh()
  }

  return (
    <>
      <CrudPage
        title="Amigos"
        items={crud.items}
        columns={COLUMNS}
        page={crud.page}
        lastPage={crud.lastPage}
        loading={crud.loading}
        error={crud.error}
        onPageChange={crud.goToPage}
        onNew={openNew}
        onEdit={openEdit}
        onDelete={(row) => setDeleteId(row.id)}
        deleteConfirmId={deleteId}
        onDeleteConfirm={handleDeleteConfirm}
        onDeleteCancel={() => setDeleteId(null)}
      />

      <FormModal
        title={modal.editing ? 'Editar relación' : 'Nueva relación de amistad'}
        open={modal.open}
        onClose={close}
        onSubmit={handleSubmit}
        submitting={submitting}
        error={formError}
      >
        <Field label="Usuario" required>
          <select className={inputCls} value={form.user_id} onChange={(e) => set('user_id', e.target.value)} required>
            <option value="">Seleccionar usuario…</option>
            {users.map((u) => (
              <option key={u.id} value={u.id}>{u.name} ({u.email})</option>
            ))}
          </select>
        </Field>
        <Field label="Amigo" required>
          <select className={inputCls} value={form.friend_id} onChange={(e) => set('friend_id', e.target.value)} required>
            <option value="">Seleccionar usuario…</option>
            {users.map((u) => (
              <option key={u.id} value={u.id}>{u.name} ({u.email})</option>
            ))}
          </select>
        </Field>
        <Field label="Receptor (vacío = confirmado)">
          <select className={inputCls} value={form.receiver_id} onChange={(e) => set('receiver_id', e.target.value)}>
            <option value="">— Confirmado —</option>
            {users.map((u) => (
              <option key={u.id} value={u.id}>{u.name} ({u.email})</option>
            ))}
          </select>
        </Field>
      </FormModal>
    </>
  )
}
