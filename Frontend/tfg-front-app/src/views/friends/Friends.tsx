import { useState, useEffect, useMemo } from 'react'
import ReactPaginateLib from 'react-paginate'
// eslint-disable-next-line @typescript-eslint/no-explicit-any
const ReactPaginate = ((ReactPaginateLib as any).default ?? ReactPaginateLib) as typeof ReactPaginateLib
import { useAuth } from '../../context/AuthContext'
import { API_ROUTES } from '../../config/apiRoutes.js'
import type { FriendEntry } from '../../types'

const PAGE_SIZE = 8

// ─── helpers ─────────────────────────────────────────────────────────────────

function getOtherUser(entry: FriendEntry, myId: number) {
  return entry.user_id === myId ? entry.friend : entry.user
}

const COLORS = ['bg-purple-700', 'bg-blue-700', 'bg-cyan-700', 'bg-pink-700', 'bg-green-700', 'bg-orange-700']
function avatarColor(name: string) { return COLORS[name.charCodeAt(0) % COLORS.length] }

function Avatar({ name, size = 'md' }: { name: string; size?: 'sm' | 'md' }) {
  const initials = name.slice(0, 2).toUpperCase()
  const sz = size === 'sm' ? 'w-8 h-8 text-xs' : 'w-10 h-10 text-sm'
  return (
    <div className={`${sz} rounded-full ${avatarColor(name)} flex items-center justify-center font-bold text-white shrink-0`}>
      {initials}
    </div>
  )
}

function Spinner({ className = 'w-4 h-4' }: { className?: string }) {
  return (
    <svg className={`${className} animate-spin`} fill="none" viewBox="0 0 24 24">
      <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
      <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4z" />
    </svg>
  )
}

interface BackendResponse { status?: { message?: string } }
function extractMsg(json: BackendResponse, fallback: string) {
  return json?.status?.message ?? fallback
}

// ─── hooks ───────────────────────────────────────────────────────────────────

function useFriendList(token: string | null) {
  const [friends, setFriends] = useState<FriendEntry[]>([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  const load = () => {
    if (!token) return
    setLoading(true)
    fetch(API_ROUTES.FRIENDS.LIST, { headers: { Accept: 'application/json', Authorization: `Bearer ${token}` } })
      .then(async res => {
        const json = await res.json() as { data: FriendEntry[] }
        if (!res.ok) throw new Error(extractMsg(json as BackendResponse, `Error ${res.status}`))
        setFriends(json.data ?? [])
      })
      .catch(err => setError((err as Error).message))
      .finally(() => setLoading(false))
  }

  useEffect(load, [token])
  return { friends, loading, error, reload: load }
}

function usePendingList(token: string | null) {
  const [pending, setPending] = useState<FriendEntry[]>([])
  const [loading, setLoading] = useState(false)

  const load = () => {
    if (!token) return
    setLoading(true)
    fetch(API_ROUTES.FRIENDS.PENDING, { headers: { Accept: 'application/json', Authorization: `Bearer ${token}` } })
      .then(async res => {
        const json = await res.json() as { data: FriendEntry[] }
        setPending(json.data ?? [])
      })
      .catch(() => {})
      .finally(() => setLoading(false))
  }

  useEffect(load, [token])
  return { pending, loading, reload: load }
}

// ─── send-request modal ───────────────────────────────────────────────────────

interface RequestModalProps {
  token: string
  onClose: () => void
  onSuccess: () => void
}

function RequestModal({ token, onClose, onSuccess }: RequestModalProps) {
  const [username, setUsername] = useState('')
  const [status, setStatus] = useState<'idle' | 'loading' | 'success' | 'error'>('idle')
  const [msg, setMsg] = useState('')

  const submit = async () => {
    if (!username.trim()) return
    setStatus('loading')
    try {
      const res = await fetch(API_ROUTES.FRIENDS.REQUEST, {
        method: 'POST',
        headers: { Accept: 'application/json', 'Content-Type': 'application/json', Authorization: `Bearer ${token}` },
        body: JSON.stringify({ username: username.trim() }),
      })
      const json = await res.json() as BackendResponse
      if (!res.ok) throw new Error(extractMsg(json, 'Error al enviar la solicitud'))
      setStatus('success')
      setMsg(`Solicitud enviada a ${username.trim()}`)
      onSuccess()
    } catch (err) {
      setStatus('error')
      setMsg((err as Error).message)
    }
  }

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
      <div className="bg-arcade-surface border border-arcade-border rounded-xl w-full max-w-md p-6 space-y-5">
        <div className="flex items-center justify-between">
          <h2 className="text-sm font-bold text-white uppercase tracking-widest">Nueva solicitud de amistad</h2>
          <button onClick={onClose} className="text-arcade-muted hover:text-white transition-colors">
            <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18 18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        {status === 'success' ? (
          <div className="flex flex-col items-center gap-3 py-4 text-center">
            <svg className="w-10 h-10 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="m4.5 12.75 6 6 9-13.5" />
            </svg>
            <p className="text-sm text-green-400">{msg}</p>
            <button onClick={onClose} className="mt-2 px-5 py-2 rounded-lg bg-arcade-purple text-white text-xs font-semibold uppercase tracking-widest hover:bg-purple-500 transition-colors">
              Cerrar
            </button>
          </div>
        ) : (
          <>
            <div>
              <label className="block text-xs font-semibold text-arcade-muted uppercase tracking-widest mb-2">
                Nombre de usuario
              </label>
              <input
                autoFocus
                type="text"
                value={username}
                placeholder="Escribe el nombre exacto..."
                onChange={e => { setUsername(e.target.value); setStatus('idle') }}
                onKeyDown={e => { if (e.key === 'Enter') submit() }}
                className="w-full px-3 py-2.5 bg-arcade-card border border-arcade-border rounded-lg text-sm text-white placeholder-arcade-muted focus:outline-none focus:border-arcade-purple transition-colors"
              />
            </div>

            {status === 'error' && (
              <p className="text-xs text-red-400">{msg}</p>
            )}

            <div className="flex gap-3">
              <button
                onClick={submit}
                disabled={!username.trim() || status === 'loading'}
                className="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg bg-arcade-purple text-white text-xs font-semibold uppercase tracking-widest hover:bg-purple-500 transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
              >
                {status === 'loading' ? <Spinner /> : 'Enviar solicitud'}
              </button>
              <button onClick={onClose} className="px-4 py-2.5 rounded-lg text-xs font-semibold text-arcade-muted uppercase tracking-widest border border-arcade-border hover:border-arcade-purple hover:text-white transition-colors">
                Cancelar
              </button>
            </div>
          </>
        )}
      </div>
    </div>
  )
}

// ─── pending-requests modal ───────────────────────────────────────────────────

interface PendingModalProps {
  pending: FriendEntry[]
  loading: boolean
  myId: number
  token: string
  onClose: () => void
  onUpdate: () => void
}

function PendingModal({ pending, loading, myId, token, onClose, onUpdate }: PendingModalProps) {
  const [actionId, setActionId] = useState<number | null>(null)

  const act = async (entry: FriendEntry, action: 'accept' | 'reject') => {
    setActionId(entry.id)
    const url = action === 'accept' ? API_ROUTES.FRIENDS.ACCEPT(entry.id) : API_ROUTES.FRIENDS.REJECT(entry.id)
    const method = action === 'accept' ? 'PUT' : 'DELETE'
    try {
      await fetch(url, { method, headers: { Accept: 'application/json', Authorization: `Bearer ${token}` } })
      onUpdate()
    } catch {}
    setActionId(null)
  }

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
      <div className="bg-arcade-surface border border-arcade-border rounded-xl w-full max-w-lg p-6 space-y-5 max-h-[80vh] flex flex-col">
        <div className="flex items-center justify-between shrink-0">
          <h2 className="text-sm font-bold text-white uppercase tracking-widest">Solicitudes recibidas</h2>
          <button onClick={onClose} className="text-arcade-muted hover:text-white transition-colors">
            <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18 18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div className="flex-1 overflow-y-auto space-y-2 min-h-0">
          {loading ? (
            <div className="flex justify-center py-8"><Spinner className="w-6 h-6 text-arcade-muted" /></div>
          ) : pending.length === 0 ? (
            <div className="flex flex-col items-center justify-center py-12 gap-3 text-center">
              <svg className="w-10 h-10 text-arcade-border" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1}
                  d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3M13.5 4.5 15 6m0 0 1.5 1.5M15 6l1.5-1.5M15 6l-1.5 1.5M9 8.25a4.5 4.5 0 1 0 0 9 4.5 4.5 0 0 0 0-9Z" />
              </svg>
              <p className="text-arcade-muted text-xs">No tienes solicitudes pendientes</p>
            </div>
          ) : (
            pending.map(entry => {
              const sender = getOtherUser(entry, myId)
              if (!sender) return null
              const busy = actionId === entry.id
              return (
                <div key={entry.id} className="flex items-center gap-3 px-4 py-3 bg-arcade-card rounded-lg border border-arcade-border/50">
                  <Avatar name={sender.name} size="sm" />
                  <span className="flex-1 text-sm text-white font-medium uppercase tracking-wide truncate">{sender.name}</span>
                  <div className="flex gap-2 shrink-0">
                    <button
                      onClick={() => act(entry, 'accept')}
                      disabled={busy}
                      className="px-3 py-1.5 rounded bg-arcade-green/20 border border-arcade-green/40 text-arcade-green text-xs font-semibold uppercase tracking-widest hover:bg-arcade-green/30 transition-colors disabled:opacity-40"
                    >
                      {busy ? <Spinner className="w-3.5 h-3.5" /> : 'Aceptar'}
                    </button>
                    <button
                      onClick={() => act(entry, 'reject')}
                      disabled={busy}
                      className="px-3 py-1.5 rounded bg-red-900/20 border border-red-700/40 text-red-400 text-xs font-semibold uppercase tracking-widest hover:bg-red-900/30 transition-colors disabled:opacity-40"
                    >
                      Rechazar
                    </button>
                  </div>
                </div>
              )
            })
          )}
        </div>
      </div>
    </div>
  )
}

// ─── friend card ─────────────────────────────────────────────────────────────

interface FriendCardProps {
  entry: FriendEntry
  myId: number
  token: string
  onRemoved: () => void
}

function FriendCard({ entry, myId, token, onRemoved }: FriendCardProps) {
  const other = getOtherUser(entry, myId)
  const [removing, setRemoving] = useState(false)
  const [confirmRemove, setConfirmRemove] = useState(false)

  if (!other) return null

  const remove = async () => {
    setRemoving(true)
    try {
      await fetch(API_ROUTES.FRIENDS.REMOVE(entry.id), {
        method: 'DELETE',
        headers: { Accept: 'application/json', Authorization: `Bearer ${token}` },
      })
      onRemoved()
    } catch {}
    setRemoving(false)
  }

  return (
    <div className="bg-arcade-surface border border-arcade-border rounded-xl p-4 flex flex-col gap-4 hover:border-arcade-border/80 transition-colors">
      {/* User info */}
      <div className="flex items-center gap-3">
        <Avatar name={other.name} />
        <div className="min-w-0">
          <p className="text-white font-semibold text-sm uppercase tracking-wide truncate">{other.name}</p>
          <p className="text-arcade-muted text-xs truncate">{other.email}</p>
        </div>
      </div>

      {/* Actions */}
      <div className="flex flex-col gap-2">
        {/* Scores */}
        <button
          type="button"
          className="w-full flex items-center gap-2 px-3 py-2 rounded-lg bg-arcade-card border border-arcade-border/50 text-xs text-gray-300 hover:text-white hover:bg-white/5 transition-colors"
        >
          <svg className="w-3.5 h-3.5 text-arcade-cyan shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5}
              d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
          </svg>
          Ver puntuaciones
        </button>

        {/* Logros */}
        <button
          type="button"
          className="w-full flex items-center gap-2 px-3 py-2 rounded-lg bg-arcade-card border border-arcade-border/50 text-xs text-gray-300 hover:text-white hover:bg-white/5 transition-colors"
        >
          <svg className="w-3.5 h-3.5 text-yellow-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5}
              d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
          </svg>
          Ver logros
        </button>

        {/* Remove */}
        {!confirmRemove ? (
          <button
            type="button"
            onClick={() => setConfirmRemove(true)}
            className="w-full flex items-center gap-2 px-3 py-2 rounded-lg bg-arcade-card border border-red-900/30 text-xs text-red-400/70 hover:text-red-400 hover:border-red-700/50 hover:bg-red-900/10 transition-colors"
          >
            <svg className="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5}
                d="M22 10.5h-6m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM4 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 10.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
            </svg>
            Eliminar amistad
          </button>
        ) : (
          <div className="flex gap-2">
            <button
              onClick={remove}
              disabled={removing}
              className="flex-1 flex items-center justify-center py-2 rounded-lg bg-red-900/30 border border-red-700/50 text-xs text-red-400 font-semibold uppercase hover:bg-red-900/50 transition-colors disabled:opacity-40"
            >
              {removing ? <Spinner className="w-3.5 h-3.5" /> : 'Confirmar'}
            </button>
            <button
              onClick={() => setConfirmRemove(false)}
              className="flex-1 py-2 rounded-lg text-xs text-arcade-muted font-semibold uppercase border border-arcade-border hover:text-white hover:border-arcade-purple transition-colors"
            >
              Cancelar
            </button>
          </div>
        )}
      </div>
    </div>
  )
}

// ─── main view ────────────────────────────────────────────────────────────────

export function Friends() {
  const { user, token } = useAuth()
  const myId = user?.id ?? 0

  const { friends, loading: friendsLoading, error: friendsError, reload: reloadFriends } = useFriendList(token)
  const { pending, loading: pendingLoading, reload: reloadPending } = usePendingList(token)

  const [search, setSearch] = useState('')
  const [page, setPage] = useState(0)
  const [showRequest, setShowRequest] = useState(false)
  const [showPending, setShowPending] = useState(false)

  const filtered = useMemo(
    () => friends.filter(e => {
      const other = getOtherUser(e, myId)
      return !search || other?.name.toLowerCase().includes(search.toLowerCase())
    }),
    [friends, search, myId],
  )

  const totalPages = Math.ceil(filtered.length / PAGE_SIZE)
  const pageItems = filtered.slice(page * PAGE_SIZE, (page + 1) * PAGE_SIZE)

  const handleSearch = (v: string) => { setSearch(v); setPage(0) }

  const handleUpdate = () => { reloadFriends(); reloadPending() }

  return (
    <main className="max-w-6xl mx-auto px-6 py-12">

      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-6 mb-10">
        <div>
          <h1 className="text-4xl md:text-5xl font-bold text-white uppercase tracking-widest leading-tight">
            Amigos
          </h1>
          <p className="text-arcade-muted text-xs uppercase tracking-widest mt-2">
            {friends.length} {friends.length === 1 ? 'amigo' : 'amigos'}
          </p>
        </div>

        <div className="flex gap-3 shrink-0">
          {/* Solicitudes pendientes */}
          <button
            onClick={() => setShowPending(true)}
            className="relative flex items-center gap-2 px-4 py-2.5 bg-arcade-surface border border-arcade-border rounded-lg text-xs font-semibold text-gray-300 uppercase tracking-widest hover:border-arcade-purple hover:text-white transition-colors"
          >
            <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5}
                d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
            </svg>
            Solicitudes
            {pending.length > 0 && (
              <span className="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-arcade-purple text-white text-xs flex items-center justify-center font-bold">
                {pending.length > 9 ? '9+' : pending.length}
              </span>
            )}
          </button>

          {/* Nueva solicitud */}
          <button
            onClick={() => setShowRequest(true)}
            className="flex items-center gap-2 px-4 py-2.5 bg-arcade-purple rounded-lg text-xs font-semibold text-white uppercase tracking-widest hover:bg-purple-500 transition-colors"
          >
            <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5}
                d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3M13.5 4.5 15 6m0 0 1.5 1.5M15 6l1.5-1.5M15 6l-1.5 1.5M9 8.25a4.5 4.5 0 1 0 0 9 4.5 4.5 0 0 0 0-9Z" />
            </svg>
            Añadir amigo
          </button>
        </div>
      </div>

      {/* Search */}
      <div className="relative mb-8 max-w-sm">
        <svg className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-arcade-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="m21 21-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0Z" />
        </svg>
        <input
          type="text"
          placeholder="Filtrar por nombre..."
          value={search}
          onChange={e => handleSearch(e.target.value)}
          className="w-full pl-9 pr-4 py-2.5 bg-arcade-surface border border-arcade-border rounded-lg text-sm text-white placeholder-arcade-muted focus:outline-none focus:border-arcade-purple transition-colors"
        />
      </div>

      {/* States */}
      {friendsLoading && (
        <div className="flex justify-center py-24">
          <Spinner className="w-8 h-8 text-arcade-muted" />
        </div>
      )}

      {!friendsLoading && friendsError && (
        <div className="flex flex-col items-center gap-3 py-20 text-center">
          <p className="text-red-400 text-sm">{friendsError}</p>
        </div>
      )}

      {!friendsLoading && !friendsError && friends.length === 0 && (
        <div className="flex flex-col items-center justify-center py-24 gap-4 text-center">
          <svg className="w-12 h-12 text-arcade-border" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1}
              d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
          </svg>
          <p className="text-arcade-muted text-sm">Aún no tienes amigos. ¡Añade a alguien!</p>
          <button onClick={() => setShowRequest(true)} className="text-arcade-purple text-sm hover:underline">
            Enviar primera solicitud →
          </button>
        </div>
      )}

      {!friendsLoading && !friendsError && friends.length > 0 && filtered.length === 0 && (
        <div className="flex flex-col items-center justify-center py-20 gap-3 text-center">
          <p className="text-arcade-muted text-sm">No se encontraron amigos con ese nombre.</p>
        </div>
      )}

      {/* Grid */}
      {!friendsLoading && !friendsError && pageItems.length > 0 && (
        <>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-8">
            {pageItems.map(entry => (
              <FriendCard
                key={entry.id}
                entry={entry}
                myId={myId}
                token={token!}
                onRemoved={handleUpdate}
              />
            ))}
          </div>

          {/* Pagination */}
          {totalPages > 1 && (
            <div className="flex items-center justify-between border-t border-arcade-border pt-4">
              <span className="text-xs text-arcade-muted uppercase tracking-wider">
                Página {page + 1} de {totalPages}
              </span>
              <ReactPaginate
                pageCount={totalPages}
                pageRangeDisplayed={3}
                marginPagesDisplayed={1}
                forcePage={page}
                onPageChange={({ selected }) => setPage(selected)}
                previousLabel={
                  <svg className="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15.75 19.5 8.25 12l7.5-7.5" />
                  </svg>
                }
                nextLabel={
                  <svg className="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                  </svg>
                }
                breakLabel="…"
                containerClassName="flex items-center gap-1"
                pageClassName="w-7 h-7 flex items-center justify-center rounded text-xs text-arcade-muted hover:bg-white/10 hover:text-white transition-colors cursor-pointer"
                pageLinkClassName="flex items-center justify-center w-full h-full"
                activeClassName="!bg-arcade-purple !text-white"
                previousClassName="w-7 h-7 flex items-center justify-center rounded text-arcade-muted hover:bg-white/10 hover:text-white transition-colors cursor-pointer"
                previousLinkClassName="flex items-center justify-center w-full h-full"
                nextClassName="w-7 h-7 flex items-center justify-center rounded text-arcade-muted hover:bg-white/10 hover:text-white transition-colors cursor-pointer"
                nextLinkClassName="flex items-center justify-center w-full h-full"
                breakClassName="w-7 h-7 flex items-center justify-center text-arcade-muted text-xs"
                disabledClassName="opacity-30 cursor-not-allowed"
              />
            </div>
          )}
        </>
      )}

      {/* Modals */}
      {showRequest && (
        <RequestModal
          token={token!}
          onClose={() => setShowRequest(false)}
          onSuccess={handleUpdate}
        />
      )}

      {showPending && (
        <PendingModal
          pending={pending}
          loading={pendingLoading}
          myId={myId}
          token={token!}
          onClose={() => setShowPending(false)}
          onUpdate={handleUpdate}
        />
      )}
    </main>
  )
}
