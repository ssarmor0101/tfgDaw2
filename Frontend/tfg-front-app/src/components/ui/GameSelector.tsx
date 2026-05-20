import { useState, useRef, useEffect } from 'react'
import type { Juego } from '../../types'

interface GameSelectorProps {
  games: Juego[]
  selected: Juego | null
  onSelect: (game: Juego | null) => void
  loading?: boolean
}

export function GameSelector({ games, selected, onSelect, loading }: GameSelectorProps) {
  const [query, setQuery] = useState('')
  const [open, setOpen] = useState(false)
  const ref = useRef<HTMLDivElement>(null)

  const filtered = query.trim()
    ? games.filter(g => g.name.toLowerCase().includes(query.toLowerCase()))
    : games

  useEffect(() => {
    const handler = (e: MouseEvent) => {
      if (ref.current && !ref.current.contains(e.target as Node)) setOpen(false)
    }
    document.addEventListener('mousedown', handler)
    return () => document.removeEventListener('mousedown', handler)
  }, [])

  const handleSelect = (game: Juego) => {
    onSelect(game)
    setQuery(game.name)
    setOpen(false)
  }

  const handleClear = () => {
    onSelect(null)
    setQuery('')
    setOpen(false)
  }

  return (
    <div className="relative w-72" ref={ref}>
      <div className="flex items-center bg-arcade-surface border border-arcade-border rounded-lg px-3 py-2.5 gap-2 focus-within:border-arcade-purple transition-colors">
        <svg className="w-4 h-4 text-arcade-muted shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
        </svg>
        <input
          type="text"
          value={query}
          onChange={e => { setQuery(e.target.value); setOpen(true) }}
          onFocus={() => setOpen(true)}
          placeholder={loading ? 'Cargando juegos...' : 'Buscar por nombre de juego...'}
          disabled={loading}
          className="flex-1 bg-transparent text-sm text-white placeholder-arcade-muted outline-none min-w-0"
        />
        <div className="flex items-center gap-1 shrink-0">
          {selected && (
            <button onClick={handleClear} className="text-arcade-muted hover:text-white transition-colors" aria-label="Limpiar">
              <svg className="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18 18 6M6 6l12 12" />
              </svg>
            </button>
          )}
          <svg
            className={`w-4 h-4 text-arcade-muted transition-transform ${open ? 'rotate-180' : ''}`}
            fill="none" viewBox="0 0 24 24" stroke="currentColor"
          >
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="m19.5 8.25-7.5 7.5-7.5-7.5" />
          </svg>
        </div>
      </div>

      {open && filtered.length > 0 && (
        <div className="absolute top-full left-0 right-0 mt-1 bg-arcade-surface border border-arcade-border rounded-lg shadow-xl overflow-hidden z-50 max-h-60 overflow-y-auto">
          {filtered.map(game => (
            <button
              key={game.id}
              onClick={() => handleSelect(game)}
              className={`w-full text-left px-4 py-2.5 text-sm hover:bg-white/5 transition-colors ${
                selected?.id === game.id ? 'text-arcade-purple' : 'text-gray-300'
              }`}
            >
              {game.name}
            </button>
          ))}
        </div>
      )}

      {open && query.trim() && filtered.length === 0 && (
        <div className="absolute top-full left-0 right-0 mt-1 bg-arcade-surface border border-arcade-border rounded-lg shadow-xl z-50">
          <p className="px-4 py-3 text-sm text-arcade-muted">Sin resultados para "{query}"</p>
        </div>
      )}
    </div>
  )
}
