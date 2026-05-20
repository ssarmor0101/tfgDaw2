import { useState, useMemo } from 'react'
import ReactPaginateLib from 'react-paginate'
// eslint-disable-next-line @typescript-eslint/no-explicit-any
const ReactPaginate = ((ReactPaginateLib as any).default ?? ReactPaginateLib) as typeof ReactPaginateLib
import type { Juego, ScoreEntry, ApiState } from '../../types'
import { useGames as useJuegos } from '../../hooks/useGames'
import { GameSelector } from '../../components/ui/GameSelector'
import { LoadingSpinner } from '../../components/ui/LoadingSpinner'
import { ErrorMessage } from '../../components/ui/ErrorMessage'
import { API_ROUTES } from '../../config/apiRoutes.js'

const PAGE_SIZE = 10

// ----- helpers -----

function formatDate(iso: string | null): string {
  if (!iso) return '—'
  const d = new Date(iso)
  return d.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' })
    .toUpperCase()
    .replace('.', '')
}

function formatScore(n: number): string {
  return n.toLocaleString('es-ES')
}

function rankColor(rank: number) {
  if (rank === 1) return 'text-yellow-400'
  if (rank === 2) return 'text-gray-300'
  if (rank === 3) return 'text-amber-600'
  return 'text-arcade-muted'
}

function Avatar({ name }: { name: string }) {
  const initials = name.slice(0, 2).toUpperCase()
  const colors = [
    'bg-purple-700', 'bg-blue-700', 'bg-cyan-700',
    'bg-pink-700', 'bg-green-700', 'bg-orange-700',
  ]
  const color = colors[name.charCodeAt(0) % colors.length]
  return (
    <div className={`w-8 h-8 rounded ${color} flex items-center justify-center text-xs font-bold text-white shrink-0`}>
      {initials}
    </div>
  )
}

// ----- scores hook -----

function useScores(gameId: number | null): ApiState<ScoreEntry[]> & { refetch: () => void } {
  const [tick, setTick] = useState(0)
  const [state, setState] = useState<ApiState<ScoreEntry[]>>({ data: null, loading: false, error: null })

  useMemo(() => {
    if (gameId === null) {
      setState({ data: null, loading: false, error: null })
      return
    }
    let cancelled = false
    setState({ data: null, loading: true, error: null })

    fetch(API_ROUTES.SCORES.BY_GAME(gameId), { headers: { Accept: 'application/json' } })
      .then(async res => {
        if (!res.ok) throw new Error(`Error ${res.status}`)
        const json = await res.json() as { data: ScoreEntry[] }
        if (!cancelled) setState({ data: json.data, loading: false, error: null })
      })
      .catch(err => {
        if (!cancelled) setState({ data: null, loading: false, error: (err as Error).message })
      })

    return () => { cancelled = true }
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [gameId, tick])

  return { ...state, refetch: () => setTick(t => t + 1) }
}

// ----- main view -----

export function Scores() {
  const [selectedGame, setSelectedGame] = useState<Juego | null>(null)
  const [page, setPage] = useState(0)

  // fetch all games for the selector
  const { data: rawGames, loading: gamesLoading } = useJuegos(API_ROUTES.GAMES.ALL)
  const games = rawGames ?? []

  // fetch scores for selected game
  const { data: scores, loading: scoresLoading, error: scoresError, refetch } = useScores(selectedGame?.id ?? null)

  // sort by puntuacion desc
  const sorted = useMemo(
    () => [...(scores ?? [])].sort((a, b) => b.puntuacion - a.puntuacion),
    [scores],
  )

  const totalPages = Math.ceil(sorted.length / PAGE_SIZE)
  const pageItems = sorted.slice(page * PAGE_SIZE, (page + 1) * PAGE_SIZE)

  const handleGameSelect = (game: Juego | null) => {
    setSelectedGame(game)
    setPage(0)
  }

  return (
    <main className="max-w-5xl mx-auto px-6 py-12">

      {/* Header */}
      <div className="flex flex-col md:flex-row md:items-start md:justify-between gap-6 mb-10">
        <div>
          <h1 className="text-4xl md:text-5xl font-bold text-white uppercase tracking-widest leading-tight">
            Tabla de Puntuaciones
          </h1>
          <p className="text-arcade-muted text-xs uppercase tracking-widest mt-2">
            Global Rankings &amp; Hall of Fame
          </p>
        </div>
        <div className="shrink-0 mt-1">
          <GameSelector
            games={games}
            selected={selectedGame}
            onSelect={handleGameSelect}
            loading={gamesLoading}
          />
        </div>
      </div>

      {/* Game title */}
      {selectedGame && (
        <div className="mb-6 flex items-center gap-3">
          <span className="w-1 h-6 bg-arcade-purple rounded-full" />
          <span className="text-white text-lg font-semibold tracking-wide uppercase">
            {selectedGame.name}
          </span>
        </div>
      )}

      {/* Empty state */}
      {!selectedGame && (
        <div className="flex flex-col items-center justify-center py-24 gap-4 text-center">
          <svg className="w-12 h-12 text-arcade-border" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z" />
          </svg>
          <p className="text-arcade-muted text-sm">Selecciona un juego para ver su tabla de puntuaciones</p>
        </div>
      )}

      {/* Loading */}
      {selectedGame && scoresLoading && <LoadingSpinner />}

      {/* Error */}
      {selectedGame && scoresError && <ErrorMessage message={scoresError} onRetry={refetch} />}

      {/* Table */}
      {selectedGame && !scoresLoading && !scoresError && scores !== null && (
        <>
          {sorted.length === 0 ? (
            <div className="flex flex-col items-center justify-center py-20 gap-3 text-center">
              <p className="text-arcade-muted text-sm">Aún no hay puntuaciones registradas para este juego.</p>
            </div>
          ) : (
            <div className="bg-arcade-surface border border-arcade-border rounded-xl overflow-hidden">
              {/* Table head */}
              <div className="grid grid-cols-[80px_1fr_160px_140px] px-6 py-3 border-b border-arcade-border">
                {['Rango', 'Jugador', 'Puntuación', 'Fecha'].map(h => (
                  <span key={h} className="text-xs font-semibold text-arcade-muted uppercase tracking-widest">
                    {h}
                  </span>
                ))}
              </div>

              {/* Rows */}
              {pageItems.map((entry, i) => {
                const rank = page * PAGE_SIZE + i + 1
                const playerName = entry.user?.name ?? `Usuario #${entry.user_id}`
                return (
                  <div
                    key={entry.id}
                    className="grid grid-cols-[80px_1fr_160px_140px] px-6 py-4 border-b border-arcade-border/50 last:border-0 hover:bg-white/[0.03] transition-colors"
                  >
                    {/* Rank */}
                    <span className={`font-bold text-lg tabular-nums ${rankColor(rank)}`}>
                      {String(rank).padStart(2, '0')}
                    </span>

                    {/* Player */}
                    <div className="flex items-center gap-3">
                      <Avatar name={playerName} />
                      <span className="text-white text-sm font-medium uppercase tracking-wide">
                        {playerName}
                      </span>
                    </div>

                    {/* Score */}
                    <span className="text-arcade-cyan font-semibold tabular-nums text-sm self-center">
                      {formatScore(entry.puntuacion)}
                    </span>

                    {/* Date */}
                    <span className="text-arcade-muted text-xs uppercase tracking-wider self-center">
                      {formatDate(entry.created_at)}
                    </span>
                  </div>
                )
              })}

              {/* Pagination footer */}
              <div className="flex items-center justify-between px-6 py-3 border-t border-arcade-border">
                <span className="text-xs text-arcade-muted uppercase tracking-wider">
                  Mostrando {Math.min(page * PAGE_SIZE + 1, sorted.length)}–{Math.min((page + 1) * PAGE_SIZE, sorted.length)} de {sorted.length} jugadores
                </span>
                {totalPages > 1 && (
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
                )}
              </div>
            </div>
          )}
        </>
      )}
    </main>
  )
}
