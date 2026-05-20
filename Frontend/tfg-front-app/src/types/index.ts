export interface Rol {
  id: number
  name: string
  slug: 'admin' | 'user'
}

export interface User {
  id: number
  name: string
  email: string
  rol_id: number
  rol?: Rol
}

export interface Juego {
  id: number
  name: string
  description: string | null
  puntuaciones_count?: number | null
  created_at?: string | null
}

export interface ScoreEntry {
  id: number
  user_id: number
  juego_id: number
  puntuacion: number
  created_at: string | null
  user: { id: number; name: string; email: string } | null
  juego: { id: number; name: string } | null
}

export interface ApiState<T> {
  data: T | null
  loading: boolean
  error: string | null
}

export interface FriendEntry {
  id: number
  user_id: number
  friend_id: number
  receiver_id: number | null
  user: { id: number; name: string; email: string } | null
  friend: { id: number; name: string; email: string } | null
}

export interface AuthResponse {
  data: {
    token: string
    user: User
  }
}

export interface Logro {
  id: number
  name: string
  description: string | null
  juego_id: number
  juego?: { id: number; name: string } | null
}

export interface Puntuacion {
  id: number
  user_id: number
  juego_id: number
  puntuacion: number
  created_at: string | null
  user?: { id: number; name: string; email: string } | null
  juego?: { id: number; name: string } | null
}

export interface Resultado {
  id: number
  user_id: number
  logro_id: number
  created_at?: string | null
  user?: { id: number; name: string; email: string } | null
  logro?: { id: number; name: string; juego_id: number } | null
  juego?: { id: number; name: string } | null
}

export interface Amigo {
  id: number
  user_id: number
  friend_id: number
  receiver_id: number | null
  user?: { id: number; name: string; email: string } | null
  friend?: { id: number; name: string; email: string } | null
}
