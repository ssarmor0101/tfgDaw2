import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useAuth } from '../../context/AuthContext'
import { API_ROUTES } from '../../config/apiRoutes.js'

// ─── helpers ─────────────────────────────────────────────────────────────────

const BANNER_GRADIENTS = [
  'from-purple-900 via-indigo-900 to-teal-900',
  'from-violet-900 via-purple-900 to-cyan-900',
  'from-blue-900 via-purple-900 to-pink-900',
  'from-indigo-900 via-blue-900 to-teal-900',
]

function bannerGradient(name: string) {
  return BANNER_GRADIENTS[name.charCodeAt(0) % BANNER_GRADIENTS.length]
}

type ApiStatus = 'idle' | 'loading' | 'success' | 'error'

interface BackendError {
  status?: { message?: string }
  errors?: Record<string, string[]>
}

function extractMsg(body: BackendError, fallback: string): string {
  if (body.errors) {
    const first = Object.values(body.errors)[0]?.[0]
    if (first) return first
  }
  return body.status?.message ?? fallback
}

// ─── main view ────────────────────────────────────────────────────────────────

export function Profile() {
  const navigate = useNavigate()
  const { user, token, logout, updateUserData } = useAuth()

  // ── edit name ──
  const [editingName, setEditingName] = useState(false)
  const [nameInput, setNameInput] = useState(user?.name ?? '')
  const [nameStatus, setNameStatus] = useState<ApiStatus>('idle')
  const [nameMsg, setNameMsg] = useState('')

  // ── change password ──
  const [changingPassword, setChangingPassword] = useState(false)
  const [pwInput, setPwInput] = useState('')
  const [pwConfirm, setPwConfirm] = useState('')
  const [showPw, setShowPw] = useState(false)
  const [pwStatus, setPwStatus] = useState<ApiStatus>('idle')
  const [pwMsg, setPwMsg] = useState('')

  // ── delete account ──
  const [deleteStep, setDeleteStep] = useState<'idle' | 'confirm'>('idle')
  const [deleteStatus, setDeleteStatus] = useState<ApiStatus>('idle')
  const [deleteMsg, setDeleteMsg] = useState('')

  if (!user) return null

  const initials = user.name.slice(0, 2).toUpperCase()

  // ── handlers ──

  const saveName = async () => {
    const trimmed = nameInput.trim()
    if (!trimmed || trimmed === user.name) { setEditingName(false); return }
    setNameStatus('loading')
    try {
      const res = await fetch(API_ROUTES.AUTH.PROFILE, {
        method: 'PATCH',
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({ name: trimmed }),
      })
      const json = await res.json() as BackendError
      if (!res.ok) throw new Error(extractMsg(json, 'Error al actualizar'))
      updateUserData({ name: trimmed })
      setNameStatus('success')
      setNameMsg('Nombre actualizado correctamente')
      setEditingName(false)
    } catch (err) {
      setNameStatus('error')
      setNameMsg((err as Error).message)
    }
  }

  const cancelName = () => {
    setNameInput(user.name)
    setEditingName(false)
    setNameStatus('idle')
    setNameMsg('')
  }

  const savePassword = async () => {
    if (pwInput.length < 8) {
      setPwStatus('error')
      setPwMsg('La contraseña debe tener al menos 8 caracteres')
      return
    }
    if (pwInput !== pwConfirm) {
      setPwStatus('error')
      setPwMsg('Las contraseñas no coinciden')
      return
    }
    setPwStatus('loading')
    try {
      const res = await fetch(API_ROUTES.AUTH.PROFILE, {
        method: 'PATCH',
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({ password: pwInput, password_confirmation: pwConfirm }),
      })
      const json = await res.json() as BackendError
      if (!res.ok) throw new Error(extractMsg(json, 'Error al cambiar contraseña'))
      setPwStatus('success')
      setPwMsg('Contraseña cambiada correctamente')
      setPwInput('')
      setPwConfirm('')
      setChangingPassword(false)
    } catch (err) {
      setPwStatus('error')
      setPwMsg((err as Error).message)
    }
  }

  const cancelPassword = () => {
    setPwInput('')
    setPwConfirm('')
    setChangingPassword(false)
    setPwStatus('idle')
    setPwMsg('')
  }

  const deleteAccount = async () => {
    setDeleteStatus('loading')
    try {
      const res = await fetch(API_ROUTES.AUTH.PROFILE, {
        method: 'DELETE',
        headers: {
          Accept: 'application/json',
          Authorization: `Bearer ${token}`,
        },
      })
      if (!res.ok) {
        const json = await res.json() as BackendError
        throw new Error(extractMsg(json, 'Error al eliminar la cuenta'))
      }
      logout()
      navigate('/', { replace: true })
    } catch (err) {
      setDeleteStatus('error')
      setDeleteMsg((err as Error).message)
    }
  }

  return (
    <main className="max-w-3xl mx-auto px-6 py-12 space-y-5">

      {/* ── Hero card ── */}
      <div className="rounded-xl overflow-hidden border border-arcade-border">
        {/* Banner */}
        <div className={`h-28 bg-gradient-to-br ${bannerGradient(user.name)}`} />

        {/* Info row */}
        <div className="bg-arcade-surface px-6 pb-5 flex flex-col sm:flex-row sm:items-center gap-4">
          {/* Avatar overlapping banner */}
          <div className="-mt-10 w-20 h-20 rounded-full bg-arcade-bg border-4 border-arcade-surface flex items-center justify-center shrink-0">
            <span className="text-white font-bold text-2xl select-none">{initials}</span>
          </div>

          <div className="flex-1 min-w-0 sm:mt-2">
            <p className="text-white font-bold text-xl uppercase tracking-widest truncate">{user.name}</p>
            <p className="text-arcade-muted text-sm truncate">{user.email}</p>
          </div>

          <button
            onClick={() => { setEditingName(true); setNameInput(user.name) }}
            disabled={editingName}
            className="shrink-0 flex items-center gap-2 px-4 py-2 rounded-lg bg-arcade-purple/20 border border-arcade-purple/40 text-arcade-purple text-xs font-semibold uppercase tracking-widest hover:bg-arcade-purple/30 transition-colors disabled:opacity-40 disabled:cursor-not-allowed sm:mt-2"
          >
            <svg className="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2}
                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
            </svg>
            Editar perfil
          </button>
        </div>
      </div>

      {/* ── Account info card ── */}
      <div className="bg-arcade-surface border border-arcade-border rounded-xl p-6 space-y-5">
        <h2 className="flex items-center gap-2 text-sm font-semibold text-white uppercase tracking-widest">
          <svg className="w-4 h-4 text-arcade-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5}
              d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
          </svg>
          Información de cuenta
        </h2>

        {/* Fields */}
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          {/* Name field */}
          <div>
            <label className="block text-xs font-semibold text-arcade-muted uppercase tracking-widest mb-1.5">
              Nombre de usuario
            </label>
            {editingName ? (
              <input
                autoFocus
                type="text"
                value={nameInput}
                onChange={e => setNameInput(e.target.value)}
                onKeyDown={e => { if (e.key === 'Enter') saveName(); if (e.key === 'Escape') cancelName() }}
                className="w-full px-3 py-2.5 bg-arcade-card border border-arcade-purple rounded-lg text-sm text-white focus:outline-none focus:border-arcade-purple transition-colors"
              />
            ) : (
              <div className="px-3 py-2.5 bg-arcade-card border border-arcade-border rounded-lg text-sm text-white">
                {user.name}
              </div>
            )}
          </div>

          {/* Email field (read-only) */}
          <div>
            <label className="block text-xs font-semibold text-arcade-muted uppercase tracking-widest mb-1.5">
              Correo electrónico
            </label>
            <div className="px-3 py-2.5 bg-arcade-card border border-arcade-border rounded-lg text-sm text-white/60">
              {user.email}
            </div>
          </div>
        </div>

        {/* Name edit actions */}
        {editingName && (
          <div className="flex items-center gap-3">
            <button
              onClick={saveName}
              disabled={nameStatus === 'loading'}
              className="flex items-center gap-2 px-4 py-2 bg-arcade-purple rounded-lg text-xs font-semibold text-white uppercase tracking-widest hover:bg-purple-500 transition-colors disabled:opacity-50"
            >
              {nameStatus === 'loading' ? (
                <svg className="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                  <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4z" />
                </svg>
              ) : 'Guardar'}
            </button>
            <button
              onClick={cancelName}
              className="px-4 py-2 rounded-lg text-xs font-semibold text-arcade-muted uppercase tracking-widest hover:text-white border border-arcade-border hover:border-arcade-purple transition-colors"
            >
              Cancelar
            </button>
          </div>
        )}

        {/* Name feedback */}
        {nameStatus === 'success' && (
          <p className="text-xs text-green-400 flex items-center gap-1.5">
            <svg className="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="m4.5 12.75 6 6 9-13.5" />
            </svg>
            {nameMsg}
          </p>
        )}
        {nameStatus === 'error' && (
          <p className="text-xs text-red-400">{nameMsg}</p>
        )}

        <div className="border-t border-arcade-border" />

        {/* Change password toggle */}
        {!changingPassword ? (
          <button
            onClick={() => setChangingPassword(true)}
            className="flex items-center gap-2 px-4 py-2.5 rounded-lg bg-arcade-card border border-arcade-border text-xs font-semibold text-gray-300 uppercase tracking-widest hover:border-arcade-purple hover:text-white transition-colors"
          >
            <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5}
                d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
            Cambiar contraseña
          </button>
        ) : (
          <div className="space-y-4">
            <p className="text-xs font-semibold text-arcade-muted uppercase tracking-widest">Nueva contraseña</p>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div className="relative">
                <input
                  autoFocus
                  type={showPw ? 'text' : 'password'}
                  placeholder="Nueva contraseña"
                  value={pwInput}
                  onChange={e => setPwInput(e.target.value)}
                  className="w-full px-3 pr-10 py-2.5 bg-arcade-card border border-arcade-border rounded-lg text-sm text-white placeholder-arcade-muted focus:outline-none focus:border-arcade-purple transition-colors"
                />
                <button
                  type="button"
                  onClick={() => setShowPw(v => !v)}
                  className="absolute right-3 top-1/2 -translate-y-1/2 text-arcade-muted hover:text-white transition-colors"
                  tabIndex={-1}
                >
                  {showPw ? (
                    <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                  ) : (
                    <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                  )}
                </button>
              </div>

              <input
                type={showPw ? 'text' : 'password'}
                placeholder="Confirmar contraseña"
                value={pwConfirm}
                onChange={e => setPwConfirm(e.target.value)}
                onKeyDown={e => { if (e.key === 'Enter') savePassword(); if (e.key === 'Escape') cancelPassword() }}
                className="w-full px-3 py-2.5 bg-arcade-card border border-arcade-border rounded-lg text-sm text-white placeholder-arcade-muted focus:outline-none focus:border-arcade-purple transition-colors"
              />
            </div>

            {pwStatus === 'error' && <p className="text-xs text-red-400">{pwMsg}</p>}
            {pwStatus === 'success' && (
              <p className="text-xs text-green-400 flex items-center gap-1.5">
                <svg className="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="m4.5 12.75 6 6 9-13.5" />
                </svg>
                {pwMsg}
              </p>
            )}

            <div className="flex items-center gap-3">
              <button
                onClick={savePassword}
                disabled={pwStatus === 'loading'}
                className="flex items-center gap-2 px-4 py-2 bg-arcade-purple rounded-lg text-xs font-semibold text-white uppercase tracking-widest hover:bg-purple-500 transition-colors disabled:opacity-50"
              >
                {pwStatus === 'loading' ? (
                  <svg className="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4z" />
                  </svg>
                ) : 'Guardar contraseña'}
              </button>
              <button
                onClick={cancelPassword}
                className="px-4 py-2 rounded-lg text-xs font-semibold text-arcade-muted uppercase tracking-widest hover:text-white border border-arcade-border hover:border-arcade-purple transition-colors"
              >
                Cancelar
              </button>
            </div>
          </div>
        )}
      </div>

      {/* ── Danger zone ── */}
      <div className="bg-red-950/20 border border-red-900/40 rounded-xl p-6">
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div className="flex items-start gap-3">
            <svg className="w-5 h-5 text-red-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5}
                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
            <div>
              <p className="text-sm font-semibold text-red-400 uppercase tracking-widest">Zona de peligro</p>
              <p className="text-xs text-red-400/70 mt-1">Una vez que elimines tu cuenta, no podrás recuperar tus datos.</p>
            </div>
          </div>

          <div className="shrink-0">
            {deleteStep === 'idle' ? (
              <button
                onClick={() => setDeleteStep('confirm')}
                className="px-5 py-2.5 rounded-lg bg-red-900/30 border border-red-700/50 text-xs font-semibold text-red-400 uppercase tracking-widest hover:bg-red-900/50 hover:text-red-300 transition-colors"
              >
                Eliminar cuenta
              </button>
            ) : (
              <div className="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                <span className="text-xs text-red-300 uppercase tracking-widest self-center">¿Estás seguro?</span>
                <button
                  onClick={deleteAccount}
                  disabled={deleteStatus === 'loading'}
                  className="px-4 py-2 rounded-lg bg-red-600 text-xs font-semibold text-white uppercase tracking-widest hover:bg-red-500 transition-colors disabled:opacity-50"
                >
                  {deleteStatus === 'loading' ? (
                    <svg className="w-3.5 h-3.5 animate-spin mx-auto" fill="none" viewBox="0 0 24 24">
                      <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                      <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                  ) : 'Confirmar'}
                </button>
                <button
                  onClick={() => { setDeleteStep('idle'); setDeleteStatus('idle'); setDeleteMsg('') }}
                  className="px-4 py-2 rounded-lg text-xs font-semibold text-arcade-muted uppercase tracking-widest border border-arcade-border hover:border-red-700 hover:text-red-400 transition-colors"
                >
                  Cancelar
                </button>
              </div>
            )}
          </div>
        </div>

        {deleteStatus === 'error' && (
          <p className="text-xs text-red-400 mt-3">{deleteMsg}</p>
        )}
      </div>

    </main>
  )
}
