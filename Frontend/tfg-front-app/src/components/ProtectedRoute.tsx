import { Navigate } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'
import { LoadingSpinner } from './ui/LoadingSpinner'
import type { ReactNode } from 'react'

interface ProtectedRouteProps {
  children: ReactNode
  redirectTo?: string
}

export function ProtectedRoute({ children, redirectTo = '/' }: ProtectedRouteProps) {
  const { user, loading } = useAuth()

  if (loading) {
    return (
      <div className="min-h-screen bg-arcade-bg flex items-center justify-center">
        <LoadingSpinner />
      </div>
    )
  }

  if (!user) return <Navigate to={redirectTo} replace />

  return <>{children}</>
}
