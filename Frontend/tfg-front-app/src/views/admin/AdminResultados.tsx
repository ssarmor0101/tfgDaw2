import { useState, useEffect, type FormEvent } from 'react'
import { useAuth } from '../../context/AuthContext'
import { API_ROUTES } from '../../config/apiRoutes.js'
import { useCrud, apiCall } from './hooks/useCrud'
import { CrudPage } from './components/CrudPage'
import type { Column } from './components/CrudPage'
import { FormModal, Field, inputCls } from './components/FormModal'
import type { Resultado, User, Logro } from '../../types'

const COLUMNS: Column<Resultado>[] = [
  { header: 'ID', key: 'id', width: 'w-12' },
  {
    header: 'Usuario',
    key: 'user_id',
    render: (row) => row.user?.name ?? String(row.user_id),
  },
  {
    header: 'Logro',
    key: 'logro_id',
    render: (row) => row.logro?.name ?? String(row.logro_id),
  },
  {
    header: 'Juego',
    key: 'juego_id',
    render: (row) => row.juego?.name ?? '—',
  },
  {
    header: 'Fecha',
    key: 'created_at',
    render: (row) =>
      row.created_at ? new Date(row.created_at).toLocaleDateString('es-ES') : '—',
  },
]

interface FormState {
  user_id: string
  logro_id: string
}

const EMPTY: FormState = { user_id: '', logro_id: '' }

export function AdminResultados() {
  const { token } = useAuth()
  const crud = useCrud<Resultado>({ listUrl: API_ROUTES.ADMIN.RESULTADOS.LIST })

  const [users, setUsers] = useState<User[]>([])
  const [logros, setLogros] = useState<Logro[]>([])
  const [modal, setModal] = useState<{ open: boolean; editing: Resultado | null }>({ open: false, editing: null })
  const [form, setForm] = useState<FormState>(EMPTY)
  const [submitting, setSubmitting] = useState(false)
  const [formError, setFormError] = useState<string | null>(null)
  const [deleteId, setDeleteId] = useState<number | null>(null)

  useEffect(() => {
    const headers = { Authorization: `Bearer ${token}`, Accept: 'application/json' }
    Promise.all([
      fetch(API_ROUTES.ADMIN.USERS.LIST, { headers }).then((r) => r.json()),
      fetch(API_ROUTES.ADMIN.LOGROS.LIST, { headers }).then((r) => r.json()),
    ]).then(([usersJ, logrosJ]) => {
      setUsers(Array.isArray(usersJ.data) ? usersJ.data : usersJ.data?.data ?? [])
      setLogros(Array.isArray(logrosJ.data) ? logrosJ.data : logrosJ.data?.data ?? [])
    }).catch(() => {})
  }, [token])

  function openNew() {
    setForm(EMPTY)
    setFormError(null)
    setModal({ open: true, editing: null })
  }

  function openEdit(row: Resultado) {
    setForm({ user_id: String(row.user_id), logro_id: String(row.logro_id) })
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

    const body = { user_id: Number(form.user_id), logro_id: Number(form.logro_id) }
    const { editing } = modal
    const url = editing ? API_ROUTES.ADMIN.RESULTADOS.UPDATE(editing.id) : API_ROUTES.ADMIN.RESULTADOS.CREATE
    const method = editing ? 'PUT' : 'POST'

    const res = await apiCall(url, method, token, body)
    setSubmitting(false)

    if (!res.ok) { setFormError(res.message); return }
    close()
    crud.refresh()
  }

  async function handleDeleteConfirm(row: Resultado) {
    await apiCall(API_ROUTES.ADMIN.RESULTADOS.DELETE(row.id), 'DELETE', token)
    setDeleteId(null)
    crud.refresh()
  }

  return (
    <>
      <CrudPage
        title="Resultados"
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
        title={modal.editing ? 'Editar resultado' : 'Nuevo resultado'}
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
        <Field label="Logro" required>
          <select className={inputCls} value={form.logro_id} onChange={(e) => set('logro_id', e.target.value)} required>
            <option value="">Seleccionar logro…</option>
            {logros.map((l) => (
              <option key={l.id} value={l.id}>{l.name} — {l.juego?.name ?? `Juego ${l.juego_id}`}</option>
            ))}
          </select>
        </Field>
      </FormModal>
    </>
  )
}
