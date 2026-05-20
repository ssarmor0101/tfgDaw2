import { Link } from 'react-router-dom'
import { useGames } from '../../hooks/useGames'
import { GameCard } from '../../components/ui/GameCard'
import { GameCardSkeleton } from '../../components/ui/LoadingSpinner'
import { ErrorMessage } from '../../components/ui/ErrorMessage'
import { API_ROUTES } from '../../config/apiRoutes.js'

function SectionHeader({ title, showAll }: { title: string; showAll?: string }) {
  return (
    <div className="flex items-center justify-between mb-6">
      <div className="flex items-center gap-3">
        <span className="w-1 h-6 bg-arcade-purple rounded-full" />
        <h2 className="text-white font-bold text-lg tracking-widest uppercase">{title}</h2>
      </div>
      {showAll && (
        <Link
          to={showAll}
          className="text-xs text-arcade-muted uppercase tracking-widest hover:text-white transition-colors"
        >
          Ver todo
        </Link>
      )}
    </div>
  )
}

function PopularSection() {
  const { data, loading, error } = useGames(API_ROUTES.GAMES.POPULAR)

  return (
    <section>
      <SectionHeader title="Más Populares" showAll="/juegos?orden=popular" />
      {loading && (
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
          {Array.from({ length: 3 }).map((_, i) => <GameCardSkeleton key={i} />)}
        </div>
      )}
      {error && <ErrorMessage message={error} />}
      {data && (
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
          {data.slice(0, 3).map((game, i) => (
            <GameCard key={game.id} game={game} variant="featured" rank={i + 1} />
          ))}
        </div>
      )}
    </section>
  )
}

function RecentSection() {
  const { data, loading, error } = useGames(API_ROUTES.GAMES.RECENT)

  return (
    <section>
      <SectionHeader title="Más Recientes" showAll="/juegos?orden=reciente" />
      {loading && (
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          {Array.from({ length: 4 }).map((_, i) => <GameCardSkeleton key={i} />)}
        </div>
      )}
      {error && <ErrorMessage message={error} />}
      {data && (
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          {data.slice(0, 4).map((game) => (
            <GameCard key={game.id} game={game} />
          ))}
        </div>
      )}
    </section>
  )
}

export function Home() {
  return (
    <main className="max-w-7xl mx-auto px-6 py-12 space-y-14">
      <section className="py-4">
        <h1 className="text-5xl font-bold text-white uppercase tracking-widest mb-4">
          ClassicGames
        </h1>
        <p className="text-gray-400 text-lg max-w-lg">
          Bienvenido a la experiencia definitiva de juego retro-moderno.
        </p>
      </section>

      <PopularSection />
      <RecentSection />
    </main>
  )
}
