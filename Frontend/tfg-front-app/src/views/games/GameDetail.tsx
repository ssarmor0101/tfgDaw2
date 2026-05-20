import { useState, useEffect, useRef, Suspense } from 'react'
import type { ComponentType } from 'react'
import { useParams, Link } from 'react-router-dom'
import { useAuth } from '../../context/AuthContext'
import { useGames } from '../../hooks/useGames'
import { GameCard } from '../../components/ui/GameCard'
import { LoadingSpinner } from '../../components/ui/LoadingSpinner'
import { ErrorMessage } from '../../components/ui/ErrorMessage'
import { API_ROUTES } from '../../config/apiRoutes.js'
import { getGameLoader, nameToSlug, SCORE_STORAGE_KEY } from '../../games/registry'
import type { Juego } from '../../types'

// ─── hooks ───────────────────────────────────────────────────────────────────

function useGameDetail(gameId: number) {
  const [game, setGame] = useState<Juego | null>(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => {
    let cancelled = false
    setLoading(true)
    setError(null)
    fetch(API_ROUTES.GAMES.DETAIL(gameId), { headers: { Accept: 'application/json' } })
      .then(async res => {
        if (!res.ok) throw new Error(res.status === 404 ? 'Juego no encontrado' : `Error ${res.status}`)
        const json = await res.json() as { data: Juego }
        if (!cancelled) setGame(json.data)
      })
      .catch(err => { if (!cancelled) setError((err as Error).message) })
      .finally(() => { if (!cancelled) setLoading(false) })
    return () => { cancelled = true }
  }, [gameId])

  return { game, loading, error }
}

function useGameComponent(slug: string | null) {
  const [Component, setComponent] = useState<ComponentType<{ gameId: number }> | null>(null)
  const [available, setAvailable] = useState<boolean | null>(null)

  useEffect(() => {
    if (!slug) return
    const loader = getGameLoader(slug)
    if (!loader) { setAvailable(false); return }
    setAvailable(null)
    loader()
      .then(mod => { setComponent(() => mod.default); setAvailable(true) })
      .catch(() => setAvailable(false))
  }, [slug])

  return { Component, available }
}

function useGameScore(gameId: number) {
  const key = SCORE_STORAGE_KEY(gameId)

  const read = () => {
    try {
      const raw = localStorage.getItem(key)
      return raw ? (JSON.parse(raw) as { score: number }) : null
    } catch { return null }
  }

  const [entry, setEntry] = useState(read)

  useEffect(() => {
    const refresh = () => setEntry(read())
    const timer = setInterval(refresh, 800)
    window.addEventListener('classicgames:score', refresh)
    window.addEventListener('storage', refresh)
    return () => {
      clearInterval(timer)
      window.removeEventListener('classicgames:score', refresh)
      window.removeEventListener('storage', refresh)
    }
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [gameId])

  return {
    score: entry?.score ?? null,
    clearScore: () => { localStorage.removeItem(key); setEntry(null) },
  }
}

function usePublishScore(gameId: number, token: string | null) {
  const [status, setStatus] = useState<'idle' | 'loading' | 'success' | 'error'>('idle')
  const [msg, setMsg] = useState('')

  const publish = async (score: number, onSuccess: () => void) => {
    if (!token) return
    setStatus('loading')
    try {
      const res = await fetch(API_ROUTES.SCORES.BY_GAME(gameId), {
        method: 'POST',
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({ puntuacion: score }),
      })
      const json = await res.json() as { status?: { message?: string } }
      if (!res.ok) throw new Error(json.status?.message ?? `Error ${res.status}`)
      setStatus('success')
      setMsg('¡Puntuación publicada correctamente!')
      onSuccess()
    } catch (err) {
      setStatus('error')
      setMsg((err as Error).message)
    }
  }

  return { status, msg, publish }
}

// ─── sub-components ───────────────────────────────────────────────────────────

function GameUnavailable() {
  return (
    <div className="w-full aspect-video bg-arcade-card flex flex-col items-center justify-center gap-4">
      <svg className="w-12 h-12 text-arcade-border" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1}
          d="M6 12a.75.75 0 0 0 .75-.75v-7.5a.75.75 0 0 0-1.5 0v7.5A.75.75 0 0 0 6 12Zm.75 2.25a.75.75 0 0 0-1.5 0v.75a.75.75 0 0 0 1.5 0v-.75ZM12 5.25a.75.75 0 0 1 .75.75v5.25a.75.75 0 0 1-1.5 0V6a.75.75 0 0 1 .75-.75Zm.75 10.5a.75.75 0 0 0-1.5 0v.75a.75.75 0 0 0 1.5 0v-.75ZM18 7.5a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V8.25A.75.75 0 0 1 18 7.5Zm.75 9a.75.75 0 0 0-1.5 0v.75a.75.75 0 0 0 1.5 0v-.75Z" />
      </svg>
      <div className="text-center">
        <p className="text-white font-semibold text-sm uppercase tracking-widest">Juego no disponible</p>
        <p className="text-arcade-muted text-xs mt-1.5 max-w-[260px]">
          La lógica de este juego aún no está implementada en el cliente
        </p>
      </div>
    </div>
  )
}

function GameLoading() {
  return (
    <div className="w-full aspect-video bg-arcade-card flex items-center justify-center">
      <LoadingSpinner />
    </div>
  )
}

// ─── main view ────────────────────────────────────────────────────────────────

export function GameDetail() {
  const { id } = useParams<{ id: string }>()
  const gameId = Number(id)

  const { user, token } = useAuth()
  const { game, loading: gameLoading, error: gameError } = useGameDetail(gameId)

  const slug = game ? nameToSlug(game.name) : null
  const { Component: GameComponent, available } = useGameComponent(slug)

  const { score, clearScore } = useGameScore(gameId)
  const { status: publishStatus, msg: publishMsg, publish } = usePublishScore(gameId, token)

  const { data: popularGames } = useGames(API_ROUTES.GAMES.POPULAR)
  const related = (popularGames ?? []).filter(g => g.id !== gameId).slice(0, 4)

  const gameAreaRef = useRef<HTMLDivElement>(null)

  const handleFullscreen = () => gameAreaRef.current?.requestFullscreen?.()

  // Condition 1: logged in | Condition 2: game logic available | Condition 3: score in localStorage
  const canPublish = !!user && available === true && score !== null && publishStatus !== 'success'

  // ── loading / error states ──
  if (gameLoading) return <main className="max-w-7xl mx-auto px-6 py-12"><LoadingSpinner /></main>
  if (gameError || !game) return <main className="max-w-7xl mx-auto px-6 py-12"><ErrorMessage message={gameError ?? 'Juego no encontrado'} /></main>

  return (
    <main className="max-w-7xl mx-auto px-6 py-8 space-y-12">

      {/* Breadcrumb */}
      <nav className="flex items-center gap-2 text-xs text-arcade-muted uppercase tracking-widest">
        <Link to="/juegos" className="hover:text-white transition-colors">Juegos</Link>
        <span>/</span>
        <span className="text-white">{game.name}</span>
      </nav>

      {/* ── Main layout ── */}
      <div className="flex flex-col lg:flex-row gap-6">

        {/* Game area */}
        <div className="flex-1 min-w-0">
          <div ref={gameAreaRef} className="w-full bg-black rounded-t-xl overflow-hidden">
            {available === null && slug ? (
              <GameLoading />
            ) : available === true && GameComponent ? (
              <Suspense fallback={<GameLoading />}>
                <GameComponent gameId={gameId} />
              </Suspense>
            ) : (
              <GameUnavailable />
            )}
          </div>

          {/* Status bar */}
          <div className="bg-arcade-surface border border-t-0 border-arcade-border rounded-b-xl px-4 py-2 flex items-center justify-between">
            <span className="flex items-center gap-1.5 text-xs text-arcade-muted uppercase tracking-widest">
              <span className={`w-1.5 h-1.5 rounded-full ${available === true ? 'bg-arcade-green' : 'bg-gray-600'}`} />
              {available === true ? 'Juego activo' : available === false ? 'No disponible' : 'Cargando…'}
            </span>
            <button
              onClick={handleFullscreen}
              disabled={available !== true}
              title="Pantalla completa"
              className="w-8 h-8 flex items-center justify-center rounded text-arcade-muted hover:text-white hover:bg-white/5 transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
            >
              <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5}
                  d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
              </svg>
            </button>
          </div>
        </div>

        {/* ── Info panel ── */}
        <div className="lg:w-80 xl:w-96 shrink-0">
          <div className="bg-arcade-surface border border-arcade-border rounded-xl p-5 flex flex-col gap-5 h-full">

            {/* Badge + Title */}
            <div>
              <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-arcade-green/10 border border-arcade-green/30 text-arcade-green text-xs font-semibold uppercase tracking-widest mb-3">
                <span className="w-1.5 h-1.5 rounded-full bg-arcade-green" />
                Arcade Clásico
              </span>
              <h1 className="text-2xl font-bold text-white uppercase tracking-wide leading-tight">
                {game.name}
              </h1>
            </div>

            {/* Description */}
            {game.description && (
              <div>
                <p className="text-xs font-semibold text-arcade-muted uppercase tracking-widest mb-2">
                  Descripción del juego
                </p>
                <p className="text-gray-300 text-sm leading-relaxed">{game.description}</p>
              </div>
            )}

            <div className="border-t border-arcade-border" />

            {/* ── Publish score ── */}
            <div className="flex flex-col gap-2">
              {publishStatus === 'error' && (
                <p className="text-xs text-red-400 text-center">{publishMsg}</p>
              )}

              {/* Case A: not logged in */}
              {!user && (
                <Link
                  to="/login"
                  className="w-full flex items-center justify-center gap-2 py-3 bg-arcade-card border border-arcade-border rounded-lg text-sm text-gray-300 hover:border-arcade-purple hover:text-white transition-colors"
                >
                  <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5}
                      d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                  </svg>
                  Iniciar sesión para publicar
                </Link>
              )}

              {/* Case B: logged in, published successfully */}
              {user && publishStatus === 'success' && (
                <div className="w-full flex items-center justify-center gap-2 py-3 bg-green-900/30 border border-green-700/50 rounded-lg text-sm text-green-400">
                  <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="m4.5 12.75 6 6 9-13.5" />
                  </svg>
                  {publishMsg}
                </div>
              )}

              {/* Case C: logged in, publish button (may be disabled) */}
              {user && publishStatus !== 'success' && (
                <>
                  <button
                    disabled={!canPublish || publishStatus === 'loading'}
                    onClick={() => canPublish && score !== null && publish(score, clearScore)}
                    className="w-full flex items-center justify-center gap-2 py-3 rounded-lg text-sm font-semibold uppercase tracking-widest transition-all disabled:opacity-40 disabled:cursor-not-allowed enabled:bg-gradient-to-r enabled:from-arcade-purple enabled:to-pink-600 enabled:text-white enabled:hover:brightness-110"
                  >
                    {publishStatus === 'loading' ? (
                      <svg className="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                        <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4z" />
                      </svg>
                    ) : (
                      <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5}
                          d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                      </svg>
                    )}
                    {score !== null
                      ? `Enviar ${score.toLocaleString('es-ES')} pts`
                      : 'Enviar puntuación final'}
                  </button>

                  {/* Hint below button */}
                  <p className="text-xs text-arcade-muted text-center">
                    {available === false
                      ? 'La lógica del juego no está disponible'
                      : score === null
                      ? 'Juega una partida para desbloquear el envío'
                      : `Puntuación pendiente: ${score.toLocaleString('es-ES')} pts`}
                  </p>
                </>
              )}
            </div>

            <div className="border-t border-arcade-border" />

            {/* ── Secondary actions ── */}
            <div className="flex flex-col gap-2">

              {/* Logros button */}
              <Link
                to={`/juegos/${gameId}/logros`}
                className="w-full flex items-center gap-3 px-4 py-3 bg-arcade-card rounded-lg text-sm text-gray-300 hover:text-white hover:bg-white/5 transition-colors border border-arcade-border/50 group"
              >
                <svg className="w-4 h-4 text-yellow-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5}
                    d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
                </svg>
                <span className="flex-1 text-left">Logros del juego</span>
                <svg className="w-4 h-4 text-arcade-muted group-hover:text-white transition-colors shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
              </Link>

              {/* My scores button */}
              {user ? (
                <Link
                  to={`/juegos/${gameId}/mis-puntuaciones`}
                  className="w-full flex items-center gap-3 px-4 py-3 bg-arcade-card rounded-lg text-sm text-gray-300 hover:text-white hover:bg-white/5 transition-colors border border-arcade-border/50 group"
                >
                  <svg className="w-4 h-4 text-arcade-cyan shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5}
                      d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                  </svg>
                  <span className="flex-1 text-left">Mis puntuaciones</span>
                  <svg className="w-4 h-4 text-arcade-muted group-hover:text-white transition-colors shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                  </svg>
                </Link>
              ) : (
                <div className="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm text-arcade-muted/50 border border-arcade-border/20 cursor-not-allowed select-none">
                  <svg className="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5}
                      d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                  </svg>
                  <span className="flex-1">Mis puntuaciones</span>
                  <span className="text-xs">(requiere sesión)</span>
                </div>
              )}
            </div>

          </div>
        </div>
      </div>

      {/* ── Related games ── */}
      {related.length > 0 && (
        <section>
          <div className="flex items-end justify-between mb-6">
            <div>
              <p className="text-arcade-muted text-xs uppercase tracking-widest mb-1">Más acción</p>
              <h2 className="text-2xl font-bold text-white uppercase tracking-widest">
                Juegos relacionados
              </h2>
            </div>
            <Link
              to="/juegos"
              className="flex items-center gap-1.5 text-xs text-arcade-muted uppercase tracking-widest hover:text-white transition-colors pb-1"
            >
              Ver todos
              <svg className="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="m8.25 4.5 7.5 7.5-7.5 7.5" />
              </svg>
            </Link>
          </div>
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
            {related.map(g => <GameCard key={g.id} game={g} />)}
          </div>
        </section>
      )}

    </main>
  )
}
