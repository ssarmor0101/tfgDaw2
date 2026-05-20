import { useState, useEffect, useCallback } from 'react'
import { useAuth } from '../../../context/AuthContext'
import { friendlyError, httpErrorMessage } from '../../../utils/friendlyError'

interface UseCrudOptions {
  listUrl: string
  perPage?: number
}

export interface CrudState<T> {
  items: T[]
  total: number
  page: number
  lastPage: number
  loading: boolean
  error: string | null
}

// Internal state holds the full list; pagination is client-side
interface InternalState<T> {
  all: T[]
  loading: boolean
  error: string | null
}

export function useCrud<T>(options: UseCrudOptions) {
  const { token } = useAuth()
  const { listUrl, perPage = 15 } = options

  const [internal, setInternal] = useState<InternalState<T>>({
    all: [],
    loading: true,
    error: null,
  })
  const [page, setPage] = useState(1)

  const fetchAll = useCallback(async () => {
    setInternal({ all: [], loading: true, error: null })
    try {
      let res: Response
      try {
        res = await fetch(listUrl, {
          headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
        })
      } catch (networkErr) {
        throw new Error(friendlyError(networkErr))
      }
      const json = await res.json()
      if (!res.ok) throw new Error(json.status?.message ?? httpErrorMessage(res.status))

      // Normalise: {data: [...]} or {data: {data: [...]}}
      const envelope = json.data
      let all: T[] = []
      if (Array.isArray(envelope)) {
        all = envelope
      } else if (Array.isArray(envelope?.data)) {
        all = envelope.data
      }
      setInternal({ all, loading: false, error: null })
    } catch (err: unknown) {
      const msg = friendlyError(err)
      setInternal({ all: [], loading: false, error: msg })
    }
  }, [listUrl, token])

  useEffect(() => {
    fetchAll()
  }, [fetchAll])

  // Client-side pagination slice
  const total = internal.all.length
  const lastPage = Math.max(1, Math.ceil(total / perPage))
  const safePage = Math.min(page, lastPage)
  const start = (safePage - 1) * perPage
  const items = internal.all.slice(start, start + perPage)

  function goToPage(p: number) {
    setPage(Math.max(1, Math.min(p, lastPage)))
  }

  function refresh() {
    fetchAll()
  }

  return {
    items,
    total,
    page: safePage,
    lastPage,
    loading: internal.loading,
    error: internal.error,
    goToPage,
    refresh,
  }
}

export async function apiCall(
  url: string,
  method: string,
  token: string | null,
  body?: unknown,
): Promise<{ ok: boolean; data: unknown; message: string }> {
  let res: Response
  try {
    res = await fetch(url, {
      method,
      headers: {
        Authorization: `Bearer ${token}`,
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      body: body !== undefined ? JSON.stringify(body) : undefined,
    })
  } catch (networkErr) {
    return { ok: false, data: null, message: friendlyError(networkErr) }
  }
  const json = await res.json().catch(() => ({}))
  const message =
    json.status?.message ??
    (json.errors ? Object.values(json.errors as Record<string, string[]>)[0]?.[0] : undefined) ??
    (res.ok ? 'OK' : httpErrorMessage(res.status))
  return { ok: res.ok, data: json.data ?? json, message }
}
