import type { ComponentType } from 'react'

export interface GameProps {
  gameId: number
}

type GameModule = { default: ComponentType<GameProps> }

// Vite resolves this glob statically at build time.
// Any .tsx file placed in src/games/ is automatically picked up.
const modules = import.meta.glob<GameModule>('./*.tsx')

export function getGameLoader(slug: string): (() => Promise<GameModule>) | null {
  const key = `./${slug}.tsx`
  return (modules[key] as (() => Promise<GameModule>)) ?? null
}

/**
 * Converts a game name to a filesystem-safe slug.
 * "Cyber Pong 2077"  -> "cyber-pong-2077"
 * "Súper Héroe"      -> "super-heroe"
 *
 * The corresponding game file must be: src/games/<slug>.tsx
 *
 * Game contract:
 *   - Default export: React component receiving { gameId: number }
 *   - When a game session ends, write to localStorage:
 *       localStorage.setItem(`classicgames_score_${gameId}`, JSON.stringify({ score: number }))
 *   - Then dispatch: window.dispatchEvent(new Event('classicgames:score'))
 */
export function nameToSlug(name: string): string {
  return name
    .toLowerCase()
    .normalize('NFD')
    // strip combining diacritical marks (U+0300–U+036F)
    .replace(/[̀-ͯ]/g, '')
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '')
}

export const SCORE_STORAGE_KEY = (gameId: number) => `classicgames_score_${gameId}`
