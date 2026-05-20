import { useState, useEffect, useMemo } from 'react'
import { useParams, Link, Navigate } from 'react-router-dom'
import { useAuth } from '../../context/AuthContext'
import { API_ROUTES } from '../../config/apiRoutes.js'
import { LoadingSpinner } from '../../components/ui/LoadingSpinner'
import { ErrorMessage } from '../../components/ui/ErrorMessage'
import { friendlyError, httpErrorMessage } from '../../utils/friendlyError'
import type { ScoreEntry, Juego } from '../../types'

// ─── constants ────────────────────────────────────────────────────────────────
const PER_PAGE = 15

// ─── hooks ────────────────────────────────────────────────────────────────────

function useGameName(gameId: number) {
  const [name, setName] = useState<string | null>(null)

  useEffect(() => {
    let cancelled = false
    fetch(API_ROUTES.GAMES.DETAIL(gameId), { headers: { Accept: 'application/json' } })
      .then(async r => {
        if (!r.ok) return
        const json = await r.json() as { data: Juego }
        if (!cancelled) setName(json.data.name)
      })
      .catch(() => {/* silent */})
    return () => { cancelled = true }
  }, [gameId])

  return name
}

function useMyScores(gameId: number, token: string | null) {
  const [scores, setScores] = useState<ScoreEntry[]>([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => {
    if (!token) { setLoading(false); return }
    let cancelled = false
    setLoading(true)
    setError(null)
    fetch(API_ROUTES.SCORES.MY_BY_GAME(gameId), {
      headers: { Accept: 'application/json', Authorization: `Bearer ${token}` },
    })
      .then(async r => {
        if (!r.ok) throw new Error(httpErrorMessage(r.status))
        const json = await r.json() as { data: ScoreEntry[] }
        if (!cancelled) setScores(Array.isArray(json.data) ? json.data : [])
      })
      .catch(err => { if (!cancelled) setError(friendlyError(err)) })
      .finally(() => { if (!cancelled) setLoading(false) })
    return () => { cancelled = true }
  }, [gameId, token])

  return { scores, loading, error }
}

// ─── helpers ──────────────────────────────────────────────────────────────────

function formatDate(raw: string | null): string {
  if (!raw) return '—'
  return new Intl.DateTimeFormat('es-ES', {
    day: '2-digit', month: 'short', year: 'numeric',
    hour: '2-digit', minute: '2-digit',
  }).format(new Date(raw))
}

function formatScore(n: number): string {
  return n.toLocaleString('es-ES')
}

// ─── sub-components ───────────────────────────────────────────────────────────

function MedalIcon({ rank }: { rank: number }) {
  if (rank === 1) return (
    <svg className="w-4 h-4 text-yellow-400" viewBox="0 0 24 24" fill="currentColor">
      <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 0 0 .95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 0 0-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 0 0-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 0 0-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 0 0 .951-.69l1.519-4.674Z" />
    </svg>
  )
  if (rank === 2) return (
    <svg className="w-4 h-4 text-gray-300" viewBox="0 0 24 24" fill="currentColor">
      <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 0 0 .95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 0 0-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 0 0-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 0 0-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 0 0 .951-.69l1.519-4.674Z" />
    </svg>
  )
  if (rank === 3) return (
    <svg className="w-4 h-4 text-amber-600" viewBox="0 0 24 24" fill="currentColor">
      <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 0 0 .95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 0 0-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 0 0-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 0 0-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 0 0 .951-.69l1.519-4.674Z" />
    </svg>
  )
  return <span className="text-xs text-arcade-muted tabular-nums w-4 text-center">{rank}</span>
}

function TableSkeleton() {
  return (
    <div className="animate-pulse">
      {/* header */}
      <div className="grid grid-cols-[48px_1fr_1fr] gap-4 px-5 py-3 border-b border-arcade-border">
        {[1, 2, 3].map(i => (
          <div key={i} className="h-3 bg-white/10 rounded w-16" />
        ))}
      </div>
      {/* rows */}
      {Array.from({ length: 8 }).map((_, i) => (
        <div key={i} className="grid grid-cols-[48px_1fr_1fr] gap-4 px-5 py-4 border-b border-arcade-border/30">
          <div className="h-4 w-6 bg-white/10 rounded" />
          <div className="h-4 w-24 bg-white/10 rounded" />
          <div className="h-4 w-32 bg-white/10 rounded" />
        </div>
      ))}
    </div>
  )
}

// ─── Pagination ───────────────────────────────────────────────────────────────

interface PaginationProps {
  page: number; lastPage: number; total: number; onPage: (p: number) => void
}

function Pagination({ page, lastPage, total, onPage }: PaginationProps) {
  if (lastPage <= 1) return null

  const pages: (number | '…')[] = []
  const add = (n: number) => { if (!pages.includes(n)) pages.push(n) }
  add(1)
  if (page - 2 > 2) pages.push('…')
  for (let p = Math.max(2, page - 1); p <= Math.min(lastPage - 1, page + 1); p++) add(p)
  if (page + 2 < lastPage - 1) pages.push('…')
  if (lastPage > 1) add(lastPage)

  const base = 'w-9 h-9 flex items-center justify-center rounded text-sm transition-colors'

  return (
    <div className="flex items-center justify-between gap-4 px-5 py-4 border-t border-arcade-border">
      <p className="text-xs text-arcade-muted">
        {total} partida{total !== 1 ? 's' : ''} en total
      </p>
      <div className="flex items-center gap-1">
        <button
          onClick={() => onPage(page - 1)} disabled={page === 1}
          className={`${base} ${page === 1 ? 'text-arcade-border cursor-not-allowed' : 'text-arcade-muted hover:text-white hover:bg-white/5'}`}
          aria-label="Anterior"
        >
          <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="m15.75 19.5-7.5-7.5 7.5-7.5" />
          </svg>
        </button>

        {pages.map((p, i) =>
          p === '…'
            ? <span key={`e${i}`} className="w-9 h-9 flex items-center justify-center text-arcade-muted text-sm">…</span>
            : <button key={p} onClick={() => onPage(p)}
                className={`${base} ${p === page ? 'bg-arcade-purple text-white font-semibold' : 'text-arcade-muted hover:text-white hover:bg-white/5'}`}>
                {p}
              </button>
        )}

        <button
          onClick={() => onPage(page + 1)} disabled={page === lastPage}
          className={`${base} ${page === lastPage ? 'text-arcade-border cursor-not-allowed' : 'text-arcade-muted hover:text-white hover:bg-white/5'}`}
          aria-label="Siguiente"
        >
          <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="m8.25 4.5 7.5 7.5-7.5 7.5" />
          </svg>
        </button>
      </div>
    </div>
  )
}

// ─── main view ────────────────────────────────────────────────────────────────

export function GameMisPuntuaciones() {
  const { id } = useParams<{ id: string }>()
  const gameId = Number(id)

  const { user, token, loading: authLoading } = useAuth()

  // Redirect anonymous users to login
  if (!authLoading && !user) return <Navigate to="/login" replace />

  const gameName = useGameName(gameId)
  const { scores, loading, error } = useMyScores(gameId, token)

  // Sort by score descending (best first) and assign rank
  const ranked = useMemo(
    () => [...scores].sort((a, b) => b.puntuacion - a.puntuacion),
    [scores]
  )

  // Stats
  const best    = ranked[0]?.puntuacion ?? null
  const total   = scores.length
  const average = total > 0 ? Math.round(scores.reduce((s, e) => s + e.puntuacion, 0) / total) : null

  // Pagination — same order as ranked (score desc)
  const [page, setPage] = useState(1)
  const lastPage = Math.max(1, Math.ceil(total / PER_PAGE))
  useEffect(() => { setPage(1) }, [total])

  const pageItems = useMemo(
    () => ranked.slice((page - 1) * PER_PAGE, page * PER_PAGE),
    [ranked, page]
  )

  // rankMap: entry id → position in score-sorted list (1-based)
  const rankMap = useMemo(() => {
    const map = new Map<number, number>()
    ranked.forEach((e, i) => map.set(e.id, i + 1))
    return map
  }, [ranked])


  return (
    <main className="max-w-4xl mx-auto px-6 py-8 space-y-8">

      {/* Breadcrumb */}
      <nav className="flex items-center gap-2 text-xs text-arcade-muted uppercase tracking-widest">
        <Link to="/juegos" className="hover:text-white transition-colors">Juegos</Link>
        <span>/</span>
        {gameName
          ? <Link to={`/juegos/${gameId}`} className="hover:text-white transition-colors">{gameName}</Link>
          : <span className="w-24 h-3 bg-white/10 rounded animate-pulse inline-block align-middle" />
        }
        <span>/</span>
        <span className="text-white">Mis puntuaciones</span>
      </nav>

      {/* Header */}
      <div>
        <p className="text-xs font-semibold text-arcade-cyan uppercase tracking-widest mb-1.5">
          Historial personal
        </p>
        <h1 className="text-3xl font-bold text-white">
          Mis puntuaciones en&nbsp;
          <span className="text-arcade-cyan">{gameName ?? '…'}</span>
        </h1>
        <p className="text-arcade-muted text-sm mt-2">
          Todas las partidas que has enviado a la tabla global para este juego.
        </p>
      </div>

      {/* Stats */}
      {!loading && !error && (
        <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
          {/* Best */}
          <div className="bg-arcade-surface border border-arcade-border rounded-xl p-5 flex items-start gap-4">
            <div className="w-10 h-10 rounded-lg bg-yellow-500/10 border border-yellow-500/20 flex items-center justify-center shrink-0">
              <svg className="w-5 h-5 text-yellow-400" viewBox="0 0 24 24" fill="currentColor">
                <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 0 0 .95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 0 0-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 0 0-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 0 0-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 0 0 .951-.69l1.519-4.674Z" />
              </svg>
            </div>
            <div>
              <p className="text-xs text-arcade-muted uppercase tracking-widest mb-1">Mejor puntuación</p>
              <p className="text-2xl font-bold text-white tabular-nums">
                {best !== null ? formatScore(best) : '—'}
              </p>
            </div>
          </div>

          {/* Total */}
          <div className="bg-arcade-surface border border-arcade-border rounded-xl p-5 flex items-start gap-4">
            <div className="w-10 h-10 rounded-lg bg-arcade-purple/10 border border-arcade-purple/20 flex items-center justify-center shrink-0">
              <svg className="w-5 h-5 text-arcade-purple" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5}
                  d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
              </svg>
            </div>
            <div>
              <p className="text-xs text-arcade-muted uppercase tracking-widest mb-1">Partidas jugadas</p>
              <p className="text-2xl font-bold text-white tabular-nums">{total}</p>
            </div>
          </div>

          {/* Average */}
          <div className="bg-arcade-surface border border-arcade-border rounded-xl p-5 flex items-start gap-4">
            <div className="w-10 h-10 rounded-lg bg-arcade-cyan/10 border border-arcade-cyan/20 flex items-center justify-center shrink-0">
              <svg className="w-5 h-5 text-arcade-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5}
                  d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
              </svg>
            </div>
            <div>
              <p className="text-xs text-arcade-muted uppercase tracking-widest mb-1">Puntuación media</p>
              <p className="text-2xl font-bold text-white tabular-nums">
                {average !== null ? formatScore(average) : '—'}
              </p>
            </div>
          </div>
        </div>
      )}

      {/* Table */}
      {error ? (
        <ErrorMessage message={error} />
      ) : loading ? (
        <div className="bg-arcade-surface border border-arcade-border rounded-xl overflow-hidden">
          <TableSkeleton />
        </div>
      ) : total === 0 ? (
        <div className="flex flex-col items-center justify-center py-24 gap-4 text-center
                        bg-arcade-surface border border-arcade-border rounded-xl">
          <svg className="w-12 h-12 text-arcade-border" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1}
              d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
          </svg>
          <div>
            <p className="text-white font-semibold text-sm uppercase tracking-widest">Sin partidas</p>
            <p className="text-arcade-muted text-xs mt-1.5 max-w-[260px]">
              Todavía no has enviado ninguna puntuación para este juego.
            </p>
          </div>
          <Link
            to={`/juegos/${gameId}`}
            className="mt-2 px-5 py-2.5 text-sm bg-arcade-purple text-white rounded-lg hover:bg-purple-500 transition-colors font-semibold uppercase tracking-widest"
          >
            Jugar ahora
          </Link>
        </div>
      ) : (
        <div className="bg-arcade-surface border border-arcade-border rounded-xl overflow-hidden">

          {/* Table header */}
          <div className="grid grid-cols-[48px_1fr_1fr] gap-4 px-5 py-3 border-b border-arcade-border
                          text-xs font-semibold text-arcade-muted uppercase tracking-widest">
            <span>#</span>
            <span>Puntuación</span>
            <span>Fecha</span>
          </div>

          {/* Rows */}
          <div className="divide-y divide-arcade-border/40">
            {pageItems.map(entry => {
              const rank = rankMap.get(entry.id) ?? 0
              const isBest = rank === 1

              return (
                <div
                  key={entry.id}
                  className={`grid grid-cols-[48px_1fr_1fr] gap-4 px-5 py-4 items-center
                    transition-colors hover:bg-white/[0.02]
                    ${isBest ? 'bg-yellow-500/[0.04]' : ''}`}
                >
                  {/* Rank */}
                  <div className="flex items-center justify-start">
                    <MedalIcon rank={rank} />
                  </div>

                  {/* Score */}
                  <div className="flex items-center gap-2">
                    <span className={`font-bold tabular-nums text-base
                      ${isBest ? 'text-yellow-400' : 'text-white'}`}>
                      {formatScore(entry.puntuacion)}
                    </span>
                    {isBest && (
                      <span className="text-[10px] font-semibold uppercase tracking-widest
                                       px-1.5 py-0.5 rounded bg-yellow-500/15 text-yellow-400 border border-yellow-500/20">
                        Récord
                      </span>
                    )}
                  </div>

                  {/* Date */}
                  <span className="text-sm text-arcade-muted tabular-nums">
                    {formatDate(entry.created_at)}
                  </span>
                </div>
              )
            })}
          </div>

          {/* Pagination */}
          <Pagination page={page} lastPage={lastPage} total={total} onPage={setPage} />
        </div>
      )}

    </main>
  )
}
