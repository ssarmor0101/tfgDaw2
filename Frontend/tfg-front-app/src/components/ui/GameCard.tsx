import { Link } from 'react-router-dom'
import type { Juego } from '../../types'

// Gradients determinísticos por ID del juego
const GRADIENTS = [
  'from-purple-900 via-indigo-900 to-blue-900',
  'from-cyan-900 via-blue-900 to-purple-900',
  'from-violet-900 via-purple-900 to-pink-900',
  'from-blue-900 via-cyan-900 to-teal-900',
  'from-indigo-900 via-violet-900 to-purple-900',
  'from-pink-900 via-purple-900 to-indigo-900',
]

function GamePlaceholder({ id, name }: { id: number; name: string }) {
  const gradient = GRADIENTS[id % GRADIENTS.length]
  const initials = name.slice(0, 2).toUpperCase()
  return (
    <div className={`w-full h-full bg-gradient-to-br ${gradient} flex items-center justify-center`}>
      <span className="text-white/20 font-bold text-4xl tracking-widest select-none">{initials}</span>
    </div>
  )
}

interface GameCardProps {
  game: Juego
  variant?: 'featured' | 'default'
  rank?: number
}

export function GameCard({ game, variant = 'default', rank }: GameCardProps) {
  if (variant === 'featured') {
    return (
      <Link
        to={`/juegos/${game.id}`}
        className="group relative bg-arcade-card rounded-lg overflow-hidden block hover:ring-1 hover:ring-arcade-purple transition-all"
      >
        <div className="aspect-[16/10] overflow-hidden">
          <GamePlaceholder id={game.id} name={game.name} />
        </div>
        {rank === 1 && (
          <span className="absolute top-3 left-3 flex items-center gap-1.5 bg-black/60 backdrop-blur-sm px-2 py-1 rounded text-xs font-medium text-arcade-green">
            <span className="w-1.5 h-1.5 rounded-full bg-arcade-green inline-block" />
            EN TENDENCIA
          </span>
        )}
        <div className="p-3">
          <p className="text-white font-semibold text-sm uppercase tracking-wide">{game.name}</p>
          {game.description && (
            <p className="text-arcade-muted text-xs mt-1 line-clamp-1">{game.description}</p>
          )}
        </div>
      </Link>
    )
  }

  return (
    <Link
      to={`/juegos/${game.id}`}
      className="group bg-arcade-card rounded-lg overflow-hidden block hover:ring-1 hover:ring-arcade-purple transition-all"
    >
      <div className="aspect-video overflow-hidden">
        <GamePlaceholder id={game.id} name={game.name} />
      </div>
      <div className="p-3">
        <p className="text-white font-medium text-sm">{game.name}</p>
        {game.description && (
          <p className="text-arcade-muted text-xs mt-0.5 line-clamp-1">{game.description}</p>
        )}
      </div>
    </Link>
  )
}
