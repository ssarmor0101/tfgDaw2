import { useState, useEffect, type FormEvent } from 'react'
import { useAuth } from '../../context/AuthContext'
import { API_ROUTES } from '../../config/apiRoutes.js'
import { useCrud, apiCall } from './hooks/useCrud'
import { CrudPage } from './components/CrudPage'
import type { Column } from './components/CrudPage'
import { FormModal, Field, inputCls } from './components/FormModal'
import type { Logro, Juego } from '../../types'

const COLUMNS: Column<Logro>[] = [
  { header: 'ID', key: 'id', width: 'w-12' },
  { header: 'Nombre', key: 'name' },
  {
    header: 'Descripción',
    key: 'description',
    render: (row) => <span className="truncate max-w-xs block">{row.description ?? '—'}</span>,
  },
  {
    header: 'Juego',
    key: 'juego_id',
    render: (row) => row.juego?.name ?? String(row.juego_id),
  },
]

interface FormState {
  name: string
  description: string
  juego_id: string
}

const EMPTY: FormState = { name: '', description: '', juego_id: '' }

export function AdminLogros() {
  const { token } = useAuth()
  const crud = useCrud<Logro>({ listUrl: API_ROUTES.ADMIN.LOGROS.LIST })

  const [juegos, setJuegos] = useState<Juego[]>([])
  const [modal, setModal] = useState<{ open: boolean; editing: Logro | null }>({ open: false, editing: null })
  const [form, setForm] = useState<FormState>(EMPTY)
  const [submitting, setSubmitting] = useState(false)
  const [formError, setFormError] = useState<string | null>(null)
  const [deleteId, setDeleteId] = useState<number | null>(null)

  useEffect(() => {
    fetch(API_ROUTES.GAMES.ALL, { headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' } })
      .then((r) => r.json())
      .then((j) => {
        const items = Array.isArray(j.data) ? j.data : j.data?.data ?? []
        setJuegos(items)
      })
      .catch(() => {})
  }, [token])

  function openNew() {
    setForm(EMPTY)
    setFormError(null)
    setModal({ open: true, editing: null })
  }

  function openEdit(row: Logro) {
    setForm({ name: row.name, description: row.description ?? '', juego_id: String(row.juego_id) })
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

    const body = { name: form.name, description: form.description || null, juego_id: Number(form.juego_id) }
    const { editing } = modal
    const url = editing ? API_ROUTES.ADMIN.LOGROS.UPDATE(editing.id) : API_ROUTES.ADMIN.LOGROS.CREATE
    const method = editing ? 'PUT' : 'POST'

    const res = await apiCall(url, method, token, body)
    setSubmitting(false)

    if (!res.ok) { setFormError(res.message); return }
    close()
    crud.refresh()
  }

  async function handleDeleteConfirm(row: Logro) {
    await apiCall(API_ROUTES.ADMIN.LOGROS.DELETE(row.id), 'DELETE', token)
    setDeleteId(null)
    crud.refresh()
  }

  return (
    <>
      <CrudPage
        title="Logros"
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
        title={modal.editing ? 'Editar logro' : 'Nuevo logro'}
        open={modal.open}
        onClose={close}
        onSubmit={handleSubmit}
        submitting={submitting}
        error={formError}
      >
        <Field label="Nombre" required>
          <input className={inputCls} value={form.name} onChange={(e) => set('name', e.target.value)} required />
        </Field>
        <Field label="Descripción">
          <textarea className={`${inputCls} resize-none`} rows={3} value={form.description} onChange={(e) => set('description', e.target.value)} />
        </Field>
        <Field label="Juego" required>
          <select className={inputCls} value={form.juego_id} onChange={(e) => set('juego_id', e.target.value)} required>
            <option value="">Seleccionar juego…</option>
            {juegos.map((j) => (
              <option key={j.id} value={j.id}>{j.name}</option>
            ))}
          </select>
        </Field>
      </FormModal>
    </>
  )
}
