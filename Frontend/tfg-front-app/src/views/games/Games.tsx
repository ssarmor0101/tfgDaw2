import { useState, useEffect, useRef } from 'react'
import ReactPaginateLib from 'react-paginate'
// eslint-disable-next-line @typescript-eslint/no-explicit-any
const ReactPaginate = ((ReactPaginateLib as any).default ?? ReactPaginateLib) as typeof ReactPaginateLib
import type { Juego } from '../../types'
import { GameCard } from '../../components/ui/GameCard'
import { LoadingSpinner } from '../../components/ui/LoadingSpinner'
import { ErrorMessage } from '../../components/ui/ErrorMessage'
import { API_ROUTES } from '../../config/apiRoutes.js'

type OrderOption = 'popular_desc' | 'popular_asc' | 'recent' | 'oldest'

const ORDER_LABELS: Record<OrderOption, string> = {
  popular_desc: 'Más populares',
  popular_asc: 'Menos populares',
  recent: 'Más recientes',
  oldest: 'Más antiguos',
}

interface GamesPage {
  items: Juego[]
  total: number
  per_page: number
  current_page: number
  last_page: number
}

function useGamesSearch(query: string, order: OrderOption, page: number) {
  const [data, setData] = useState<GamesPage | null>(null)
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => {
    let cancelled = false
    setLoading(true)
    setError(null)

    const params = new URLSearchParams({
      q: query,
      order,
      page: String(page),
      per_page: '12',
    })

    fetch(`${API_ROUTES.GAMES.SEARCH}?${params}`, { headers: { Accept: 'application/json' } })
      .then(async res => {
        if (!res.ok) throw new Error(`Error ${res.status}`)
        const json = await res.json() as GamesPage & { status: unknown }
        if (!cancelled) setData(json)
      })
      .catch(err => {
        if (!cancelled) setError((err as Error).message)
      })
      .finally(() => {
        if (!cancelled) setLoading(false)
      })

    return () => { cancelled = true }
  }, [query, order, page])

  return { data, loading, error }
}

export function Games() {
  const [inputValue, setInputValue] = useState('')
  const [query, setQuery] = useState('')
  const [order, setOrder] = useState<OrderOption>('popular_desc')
  const [page, setPage] = useState(1)
  const [orderOpen, setOrderOpen] = useState(false)
  const debounceRef = useRef<ReturnType<typeof setTimeout> | null>(null)

  const { data, loading, error } = useGamesSearch(query, order, page)

  const handleInput = (value: string) => {
    setInputValue(value)
    if (debounceRef.current) clearTimeout(debounceRef.current)
    debounceRef.current = setTimeout(() => {
      setQuery(value)
      setPage(1)
    }, 350)
  }

  const handleOrder = (o: OrderOption) => {
    setOrder(o)
    setPage(1)
    setOrderOpen(false)
  }

  const handlePageChange = ({ selected }: { selected: number }) => {
    setPage(selected + 1)
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }

  const games = data?.items ?? []
  const totalPages = data?.last_page ?? 1
  const total = data?.total ?? 0

  return (
    <main className="max-w-6xl mx-auto px-6 py-12">

      {/* Header */}
      <div className="mb-10">
        <h1 className="text-4xl md:text-5xl font-bold text-white uppercase tracking-widest leading-tight">
          Catálogo de Juegos
        </h1>
        <p className="text-arcade-muted text-xs uppercase tracking-widest mt-2">
          Explora todos los juegos disponibles
        </p>
      </div>

      {/* Filters */}
      <div className="flex flex-col sm:flex-row gap-3 mb-8">
        {/* Search */}
        <div className="relative flex-1">
          <svg
            className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-arcade-muted"
            fill="none" viewBox="0 0 24 24" stroke="currentColor"
          >
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2}
              d="m21 21-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0Z" />
          </svg>
          <input
            type="text"
            placeholder="Buscar juego..."
            value={inputValue}
            onChange={e => handleInput(e.target.value)}
            className="w-full pl-9 pr-4 py-2.5 bg-arcade-surface border border-arcade-border rounded-lg text-sm text-white placeholder-arcade-muted focus:outline-none focus:border-arcade-purple transition-colors"
          />
        </div>

        {/* Order dropdown */}
        <div className="relative">
          <button
            onClick={() => setOrderOpen(o => !o)}
            className="flex items-center gap-2 px-4 py-2.5 bg-arcade-surface border border-arcade-border rounded-lg text-sm text-white hover:border-arcade-purple transition-colors min-w-[170px] justify-between"
          >
            <span>{ORDER_LABELS[order]}</span>
            <svg
              className={`w-4 h-4 text-arcade-muted transition-transform ${orderOpen ? 'rotate-180' : ''}`}
              fill="none" viewBox="0 0 24 24" stroke="currentColor"
            >
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="m19 9-7 7-7-7" />
            </svg>
          </button>
          {orderOpen && (
            <div className="absolute right-0 top-full mt-1 w-full bg-arcade-surface border border-arcade-border rounded-lg overflow-hidden z-20 shadow-xl">
              {(Object.entries(ORDER_LABELS) as [OrderOption, string][]).map(([key, label]) => (
                <button
                  key={key}
                  onClick={() => handleOrder(key)}
                  className={`w-full text-left px-4 py-2.5 text-sm transition-colors hover:bg-white/5 ${
                    order === key ? 'text-arcade-purple font-semibold' : 'text-white'
                  }`}
                >
                  {label}
                </button>
              ))}
            </div>
          )}
        </div>
      </div>

      {/* Results count */}
      {!loading && !error && data && (
        <p className="text-xs text-arcade-muted uppercase tracking-wider mb-5">
          {total === 0 ? 'Sin resultados' : `${total} juego${total !== 1 ? 's' : ''} encontrado${total !== 1 ? 's' : ''}`}
        </p>
      )}

      {/* Loading */}
      {loading && <LoadingSpinner />}

      {/* Error */}
      {!loading && error && <ErrorMessage message={error} />}

      {/* Empty */}
      {!loading && !error && games.length === 0 && (
        <div className="flex flex-col items-center justify-center py-24 gap-4 text-center">
          <svg className="w-12 h-12 text-arcade-border" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1}
              d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0Z" />
          </svg>
          <p className="text-arcade-muted text-sm">No se encontraron juegos para tu búsqueda.</p>
        </div>
      )}

      {/* Grid */}
      {!loading && !error && games.length > 0 && (
        <>
          <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mb-8">
            {games.map(game => (
              <GameCard key={game.id} game={game} />
            ))}
          </div>

          {/* Pagination */}
          {totalPages > 1 && (
            <div className="flex items-center justify-between border-t border-arcade-border pt-4">
              <span className="text-xs text-arcade-muted uppercase tracking-wider">
                Página {page} de {totalPages}
              </span>
              <ReactPaginate
                pageCount={totalPages}
                pageRangeDisplayed={3}
                marginPagesDisplayed={1}
                forcePage={page - 1}
                onPageChange={handlePageChange}
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
    </main>
  )
}
