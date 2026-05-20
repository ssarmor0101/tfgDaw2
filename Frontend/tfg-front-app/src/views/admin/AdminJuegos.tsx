import { useState, type FormEvent } from 'react'
import { useAuth } from '../../context/AuthContext'
import { API_ROUTES } from '../../config/apiRoutes.js'
import { useCrud, apiCall } from './hooks/useCrud'
import { CrudPage } from './components/CrudPage'
import type { Column } from './components/CrudPage'
import { FormModal, Field, inputCls } from './components/FormModal'
import type { Juego } from '../../types'

const COLUMNS: Column<Juego>[] = [
  { header: 'ID', key: 'id', width: 'w-12' },
  { header: 'Nombre', key: 'name' },
  {
    header: 'Descripción',
    key: 'description',
    render: (row) => (
      <span className="truncate max-w-xs block">{row.description ?? '—'}</span>
    ),
  },
  {
    header: 'Puntuaciones',
    key: 'puntuaciones_count',
    render: (row) => String(row.puntuaciones_count ?? 0),
  },
]

interface FormState {
  name: string
  description: string
}

const EMPTY: FormState = { name: '', description: '' }

export function AdminJuegos() {
  const { token } = useAuth()
  const crud = useCrud<Juego>({ listUrl: API_ROUTES.GAMES.ALL })

  const [modal, setModal] = useState<{ open: boolean; editing: Juego | null }>({
    open: false,
    editing: null,
  })
  const [form, setForm] = useState<FormState>(EMPTY)
  const [submitting, setSubmitting] = useState(false)
  const [formError, setFormError] = useState<string | null>(null)
  const [deleteId, setDeleteId] = useState<number | null>(null)

  function openNew() {
    setForm(EMPTY)
    setFormError(null)
    setModal({ open: true, editing: null })
  }

  function openEdit(row: Juego) {
    setForm({ name: row.name, description: row.description ?? '' })
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

    const body = { name: form.name, description: form.description || null }
    const { editing } = modal
    const url = editing ? API_ROUTES.ADMIN.JUEGOS.UPDATE(editing.id) : API_ROUTES.ADMIN.JUEGOS.CREATE
    const method = editing ? 'PUT' : 'POST'

    const res = await apiCall(url, method, token, body)
    setSubmitting(false)

    if (!res.ok) {
      setFormError(res.message)
      return
    }
    close()
    crud.refresh()
  }

  async function handleDeleteConfirm(row: Juego) {
    await apiCall(API_ROUTES.ADMIN.JUEGOS.DELETE(row.id), 'DELETE', token)
    setDeleteId(null)
    crud.refresh()
  }

  return (
    <>
      <CrudPage
        title="Juegos"
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
        title={modal.editing ? 'Editar juego' : 'Nuevo juego'}
        open={modal.open}
        onClose={close}
        onSubmit={handleSubmit}
        submitting={submitting}
        error={formError}
      >
        <Field label="Nombre" required>
          <input
            className={inputCls}
            value={form.name}
            onChange={(e) => set('name', e.target.value)}
            required
          />
        </Field>
        <Field label="Descripción">
          <textarea
            className={`${inputCls} resize-none`}
            rows={3}
            value={form.description}
            onChange={(e) => set('description', e.target.value)}
          />
        </Field>
      </FormModal>
    </>
  )
}
