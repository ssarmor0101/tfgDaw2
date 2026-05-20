import { useState, useEffect } from 'react'
import type { Juego, ApiState } from '../types'
import { friendlyError, httpErrorMessage } from '../utils/friendlyError'

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
        if (!res.ok) throw new Error(httpErrorMessage(res.status))
        const json = (await res.json()) as { data: Juego[] }
        if (!cancelled) setState({ data: json.data, loading: false, error: null })
      } catch (err) {
        if (!cancelled) {
          setState({
            data: null,
            loading: false,
            error: friendlyError(err),
          })
        }
      }
    }

    void fetchGames()
    return () => { cancelled = true }
  }, [url])

  return state
}
