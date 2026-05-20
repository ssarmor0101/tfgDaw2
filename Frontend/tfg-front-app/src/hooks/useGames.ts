import { useState, useEffect } from 'react'
import type { Juego, ApiState } from '../types'

export function useGames(url: string) {
  const [state, setState] = useState<ApiState<Juego[]>>({
    data: null,
    loading: true,
    error: null,
  })

  useEffect(() => {
    let cancelled = false

    const fetchGames = async () => {
      setState({ data: null, loading: true, error: null })
      try {
        const res = await fetch(url, { headers: { Accept: 'application/json' } })
        if (!res.ok) throw new Error(`Error ${res.status}: ${res.statusText}`)
        const json = (await res.json()) as { data: Juego[] }
        if (!cancelled) setState({ data: json.data, loading: false, error: null })
      } catch (err) {
        if (!cancelled) {
          setState({
            data: null,
            loading: false,
            error: err instanceof Error ? err.message : 'Error desconocido',
          })
        }
      }
    }

    void fetchGames()
    return () => { cancelled = true }
  }, [url])

  return state
}
