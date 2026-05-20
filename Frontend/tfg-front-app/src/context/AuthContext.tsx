import { createContext, useContext, useState, useEffect, type ReactNode } from 'react'
import type { User, AuthResponse } from '../types'
import { API_ROUTES } from '../config/apiRoutes.js'

interface AuthContextType {
  user: User | null
  token: string | null
  loading: boolean
  login: (email: string, password: string) => Promise<void>
  register: (name: string, email: string, password: string, passwordConfirmation: string) => Promise<void>
  logout: () => void
  updateUserData: (updates: Partial<User>) => void
}

const AuthContext = createContext<AuthContextType | null>(null)

// El backend devuelve: { data: {...}, status: { success, message } }
// Los errores de validación de Laravel también pueden incluir { errors: { field: [msg] } }
interface BackendErrorBody {
  status?: { message?: string }
  errors?: Record<string, string[]>
  message?: string
}

function extractErrorMessage(body: BackendErrorBody, fallback: string): string {
  if (body.errors) {
    const first = Object.values(body.errors)[0]?.[0]
    if (first) return first
  }
  return body.status?.message ?? body.message ?? fallback
}

function persistSession(token: string, user: User) {
  localStorage.setItem('token', token)
  localStorage.setItem('user', JSON.stringify(user))
}

function clearSession() {
  localStorage.removeItem('token')
  localStorage.removeItem('user')
}

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<User | null>(null)
  const [token, setToken] = useState<string | null>(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    const savedToken = localStorage.getItem('token')
    const savedUser = localStorage.getItem('user')
    if (savedToken && savedUser) {
      setToken(savedToken)
      setUser(JSON.parse(savedUser) as User)
    }
    setLoading(false)
  }, [])

  const applySession = (data: AuthResponse['data']) => {
    setToken(data.token)
    setUser(data.user)
    persistSession(data.token, data.user)
  }

  const login = async (email: string, password: string) => {
    let res: Response
    try {
      res = await fetch(API_ROUTES.AUTH.LOGIN, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
        body: JSON.stringify({ email, password }),
      })
    } catch {
      throw new Error('No se pudo conectar con el servidor.')
    }
    if (!res.ok) {
      const body = await res.json().catch(() => ({})) as BackendErrorBody
      throw new Error(extractErrorMessage(body, 'Credenciales incorrectas'))
    }
    const { data } = (await res.json()) as AuthResponse
    applySession(data)
  }

  const register = async (
    name: string,
    email: string,
    password: string,
    passwordConfirmation: string,
  ) => {
    let res: Response
    try {
      res = await fetch(API_ROUTES.AUTH.REGISTER, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
        body: JSON.stringify({ name, email, password, password_confirmation: passwordConfirmation }),
      })
    } catch {
      throw new Error('No se pudo conectar con el servidor.')
    }
    if (!res.ok) {
      const body = await res.json().catch(() => ({})) as BackendErrorBody
      throw new Error(extractErrorMessage(body, 'Error al crear la cuenta'))
    }
    const { data } = (await res.json()) as AuthResponse
    applySession(data)
  }

  const logout = () => {
    if (token) {
      void fetch(API_ROUTES.AUTH.LOGOUT, {
        method: 'POST',
        headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
      })
    }
    setToken(null)
    setUser(null)
    clearSession()
  }

  const updateUserData = (updates: Partial<User>) => {
    if (!user) return
    const updated = { ...user, ...updates }
    setUser(updated)
    localStorage.setItem('user', JSON.stringify(updated))
  }

  return (
    <AuthContext.Provider value={{ user, token, loading, login, register, logout, updateUserData }}>
      {children}
    </AuthContext.Provider>
  )
}

export function useAuth() {
  const ctx = useContext(AuthContext)
  if (!ctx) throw new Error('useAuth must be used within AuthProvider')
  return ctx
}
