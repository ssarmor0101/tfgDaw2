import { useState, type FormEvent } from 'react'
import { useAuth } from '../../context/AuthContext'
import { API_ROUTES } from '../../config/apiRoutes.js'
import { useCrud, apiCall } from './hooks/useCrud'
import { CrudPage } from './components/CrudPage'
import type { Column } from './components/CrudPage'
import { FormModal, Field, inputCls } from './components/FormModal'
import type { User } from '../../types'

interface UserRow extends User {
  email: string
}

const ROLES = [
  { id: 1, label: 'Usuario' },
  { id: 2, label: 'Admin' },
]

const COLUMNS: Column<UserRow>[] = [
  { header: 'ID', key: 'id', width: 'w-12' },
  { header: 'Nombre', key: 'name' },
  { header: 'Email', key: 'email' },
  {
    header: 'Rol',
    key: 'rol_id',
    render: (row) => row.rol?.name ?? (row.rol_id === 2 ? 'Admin' : 'Usuario'),
  },
]

interface FormState {
  name: string
  email: string
  password: string
  password_confirmation: string
  rol_id: string
}

const EMPTY: FormState = { name: '', email: '', password: '', password_confirmation: '', rol_id: '1' }

export function AdminUsers() {
  const { token } = useAuth()
  const crud = useCrud<UserRow>({ listUrl: API_ROUTES.ADMIN.USERS.LIST })

  const [modal, setModal] = useState<{ open: boolean; editing: UserRow | null }>({
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

  function openEdit(row: UserRow) {
    setForm({ name: row.name, email: row.email, password: '', password_confirmation: '', rol_id: String(row.rol_id) })
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

    const { editing } = modal

    const body: Record<string, unknown> = {
      name: form.name,
      email: form.email,
      rol_id: Number(form.rol_id),
    }

    if (editing) {
      // Password change is opt-in: only send when the field has a value
      if (form.password) {
        body.use_password = true
        body.password = form.password
        body.password_confirmation = form.password_confirmation
      }
    } else {
      // Create: password is required with confirmation
      body.password = form.password
      body.password_confirmation = form.password_confirmation
    }

    const url = editing ? API_ROUTES.ADMIN.USERS.UPDATE(editing.id) : API_ROUTES.ADMIN.USERS.CREATE
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

  async function handleDeleteConfirm(row: UserRow) {
    await apiCall(API_ROUTES.ADMIN.USERS.DELETE(row.id), 'DELETE', token)
    setDeleteId(null)
    crud.refresh()
  }

  const isEditing = modal.editing !== null

  return (
    <>
      <CrudPage
        title="Usuarios"
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
        title={isEditing ? 'Editar usuario' : 'Nuevo usuario'}
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
        <Field label="Email" required>
          <input
            type="email"
            className={inputCls}
            value={form.email}
            onChange={(e) => set('email', e.target.value)}
            required
          />
        </Field>
        <Field label={isEditing ? 'Nueva contraseña (dejar vacío para no cambiar)' : 'Contraseña'} required={!isEditing}>
          <input
            type="password"
            className={inputCls}
            value={form.password}
            onChange={(e) => set('password', e.target.value)}
            required={!isEditing}
            autoComplete="new-password"
          />
        </Field>
        {(!isEditing || form.password) && (
          <Field label="Confirmar contraseña" required>
            <input
              type="password"
              className={inputCls}
              value={form.password_confirmation}
              onChange={(e) => set('password_confirmation', e.target.value)}
              required
              autoComplete="new-password"
            />
          </Field>
        )}
        <Field label="Rol" required>
          <select
            className={inputCls}
            value={form.rol_id}
            onChange={(e) => set('rol_id', e.target.value)}
          >
            {ROLES.map((r) => (
              <option key={r.id} value={r.id}>
                {r.label}
              </option>
            ))}
          </select>
        </Field>
      </FormModal>
    </>
  )
}
