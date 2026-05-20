import { useState, useEffect, type FormEvent } from 'react'
import { useAuth } from '../../context/AuthContext'
import { API_ROUTES } from '../../config/apiRoutes.js'
import { useCrud, apiCall } from './hooks/useCrud'
import { CrudPage } from './components/CrudPage'
import type { Column } from './components/CrudPage'
import { FormModal, Field, inputCls } from './components/FormModal'
import type { Puntuacion, User, Juego } from '../../types'

const COLUMNS: Column<Puntuacion>[] = [
  { header: 'ID', key: 'id', width: 'w-12' },
  {
    header: 'Usuario',
    key: 'user_id',
    render: (row) => row.user?.name ?? String(row.user_id),
  },
  {
    header: 'Juego',
    key: 'juego_id',
    render: (row) => row.juego?.name ?? String(row.juego_id),
  },
  { header: 'Puntuación', key: 'puntuacion' },
  {
    header: 'Fecha',
    key: 'created_at',
    render: (row) =>
      row.created_at ? new Date(row.created_at).toLocaleDateString('es-ES') : '—',
  },
]

interface FormState {
  user_id: string
  juego_id: string
  puntuacion: string
}

const EMPTY: FormState = { user_id: '', juego_id: '', puntuacion: '' }

export function AdminPuntuaciones() {
  const { token } = useAuth()
  const crud = useCrud<Puntuacion>({ listUrl: API_ROUTES.SCORES.ALL })

  const [users, setUsers] = useState<User[]>([])
  const [juegos, setJuegos] = useState<Juego[]>([])
  const [modal, setModal] = useState<{ open: boolean; editing: Puntuacion | null }>({ open: false, editing: null })
  const [form, setForm] = useState<FormState>(EMPTY)
  const [submitting, setSubmitting] = useState(false)
  const [formError, setFormError] = useState<string | null>(null)
  const [deleteId, setDeleteId] = useState<number | null>(null)

  useEffect(() => {
    const headers = { Authorization: `Bearer ${token}`, Accept: 'application/json' }
    Promise.all([
      fetch(API_ROUTES.ADMIN.USERS.LIST, { headers }).then((r) => r.json()),
      fetch(API_ROUTES.GAMES.ALL, { headers }).then((r) => r.json()),
    ]).then(([usersJ, juegosJ]) => {
      setUsers(Array.isArray(usersJ.data) ? usersJ.data : usersJ.data?.data ?? [])
      setJuegos(Array.isArray(juegosJ.data) ? juegosJ.data : juegosJ.data?.data ?? [])
    }).catch(() => {})
  }, [token])

  function openNew() {
    setForm(EMPTY)
    setFormError(null)
    setModal({ open: true, editing: null })
  }

  function openEdit(row: Puntuacion) {
    setForm({ user_id: String(row.user_id), juego_id: String(row.juego_id), puntuacion: String(row.puntuacion) })
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

    const body = {
      user_id: Number(form.user_id),
      juego_id: Number(form.juego_id),
      puntuacion: Number(form.puntuacion),
    }
    const { editing } = modal
    const url = editing ? API_ROUTES.ADMIN.PUNTUACIONES.UPDATE(editing.id) : API_ROUTES.ADMIN.PUNTUACIONES.CREATE
    const method = editing ? 'PUT' : 'POST'

    const res = await apiCall(url, method, token, body)
    setSubmitting(false)

    if (!res.ok) { setFormError(res.message); return }
    close()
    crud.refresh()
  }

  async function handleDeleteConfirm(row: Puntuacion) {
    await apiCall(API_ROUTES.ADMIN.PUNTUACIONES.DELETE(row.id), 'DELETE', token)
    setDeleteId(null)
    crud.refresh()
  }

  return (
    <>
      <CrudPage
        title="Puntuaciones"
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
        title={modal.editing ? 'Editar puntuación' : 'Nueva puntuación'}
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
        <Field label="Juego" required>
          <select className={inputCls} value={form.juego_id} onChange={(e) => set('juego_id', e.target.value)} required>
            <option value="">Seleccionar juego…</option>
            {juegos.map((j) => (
              <option key={j.id} value={j.id}>{j.name}</option>
            ))}
          </select>
        </Field>
        <Field label="Puntuación" required>
          <input
            type="number"
            min="0"
            className={inputCls}
            value={form.puntuacion}
            onChange={(e) => set('puntuacion', e.target.value)}
            required
          />
        </Field>
      </FormModal>
    </>
  )
}
