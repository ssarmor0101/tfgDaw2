import { useState, type FormEvent } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { useAuth } from '../../context/AuthContext'

function PasswordStrength({ password }: { password: string }) {
  const checks = [
    password.length >= 8,
    /[A-Z]/.test(password),
    /[0-9]/.test(password),
    /[^A-Za-z0-9]/.test(password),
  ]
  const strength = checks.filter(Boolean).length
  const colors = ['bg-arcade-border', 'bg-red-500', 'bg-yellow-500', 'bg-blue-500', 'bg-arcade-green']
  const labels = ['', 'Débil', 'Regular', 'Buena', 'Fuerte']

  if (!password) return null

  return (
    <div className="mt-2 space-y-1.5">
      <div className="flex gap-1">
        {Array.from({ length: 4 }).map((_, i) => (
          <div
            key={i}
            className={`h-1 flex-1 rounded-full transition-colors ${i < strength ? colors[strength] : 'bg-arcade-border'}`}
          />
        ))}
      </div>
      <p className="text-xs text-arcade-muted">{labels[strength]}</p>
    </div>
  )
}

export function Register() {
  const { register } = useAuth()
  const navigate = useNavigate()

  const [name, setName] = useState('')
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [passwordConfirmation, setPasswordConfirmation] = useState('')
  const [showPassword, setShowPassword] = useState(false)
  const [showConfirm, setShowConfirm] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [loading, setLoading] = useState(false)

  const passwordMismatch = passwordConfirmation.length > 0 && password !== passwordConfirmation

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault()
    if (password !== passwordConfirmation) {
      setError('Las contraseñas no coinciden')
      return
    }
    setError(null)
    setLoading(true)
    try {
      await register(name, email, password, passwordConfirmation)
      navigate('/', { replace: true })
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Error al crear la cuenta')
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="min-h-screen bg-arcade-bg flex flex-col items-center justify-center px-4 py-10">

      {/* Logo */}
      <Link to="/" className="text-white font-bold text-xl tracking-widest uppercase mb-10">
        ClassicGames
      </Link>

      <div className="w-full max-w-sm bg-arcade-surface border border-arcade-border rounded-xl p-8">
        <h1 className="text-white font-bold text-2xl mb-1">Crear cuenta</h1>
        <p className="text-arcade-muted text-sm mb-7">
          ¿Ya tienes cuenta?{' '}
          <Link to="/login" className="text-arcade-purple hover:text-purple-400 transition-colors">
            Iniciar sesión
          </Link>
        </p>

        <form onSubmit={(e) => { void handleSubmit(e) }} className="space-y-5">

          {/* Name */}
          <div className="space-y-1.5">
            <label htmlFor="name" className="block text-sm text-gray-300">
              Nombre de usuario
            </label>
            <input
              id="name"
              type="text"
              autoComplete="name"
              required
              maxLength={255}
              value={name}
              onChange={e => setName(e.target.value)}
              placeholder="Tu nombre"
              className="w-full bg-arcade-card border border-arcade-border rounded-lg px-4 py-2.5 text-sm text-white placeholder-arcade-muted outline-none focus:border-arcade-purple transition-colors"
            />
          </div>

          {/* Email */}
          <div className="space-y-1.5">
            <label htmlFor="email" className="block text-sm text-gray-300">
              Correo electrónico
            </label>
            <input
              id="email"
              type="email"
              autoComplete="email"
              required
              value={email}
              onChange={e => setEmail(e.target.value)}
              placeholder="tu@correo.com"
              className="w-full bg-arcade-card border border-arcade-border rounded-lg px-4 py-2.5 text-sm text-white placeholder-arcade-muted outline-none focus:border-arcade-purple transition-colors"
            />
          </div>

          {/* Password */}
          <div className="space-y-1.5">
            <label htmlFor="password" className="block text-sm text-gray-300">
              Contraseña <span className="text-arcade-muted">(mín. 8 caracteres)</span>
            </label>
            <div className="relative">
              <input
                id="password"
                type={showPassword ? 'text' : 'password'}
                autoComplete="new-password"
                required
                minLength={8}
                value={password}
                onChange={e => setPassword(e.target.value)}
                placeholder="••••••••"
                className="w-full bg-arcade-card border border-arcade-border rounded-lg px-4 py-2.5 pr-11 text-sm text-white placeholder-arcade-muted outline-none focus:border-arcade-purple transition-colors"
              />
              <button
                type="button"
                onClick={() => setShowPassword(v => !v)}
                className="absolute right-3 top-1/2 -translate-y-1/2 text-arcade-muted hover:text-white transition-colors"
                aria-label={showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'}
              >
                {showPassword ? (
                  <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                  </svg>
                ) : (
                  <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                  </svg>
                )}
              </button>
            </div>
            <PasswordStrength password={password} />
          </div>

          {/* Confirm Password */}
          <div className="space-y-1.5">
            <label htmlFor="confirm" className="block text-sm text-gray-300">
              Confirmar contraseña
            </label>
            <div className="relative">
              <input
                id="confirm"
                type={showConfirm ? 'text' : 'password'}
                autoComplete="new-password"
                required
                value={passwordConfirmation}
                onChange={e => setPasswordConfirmation(e.target.value)}
                placeholder="••••••••"
                className={`w-full bg-arcade-card border rounded-lg px-4 py-2.5 pr-11 text-sm text-white placeholder-arcade-muted outline-none transition-colors ${
                  passwordMismatch ? 'border-red-500 focus:border-red-400' : 'border-arcade-border focus:border-arcade-purple'
                }`}
              />
              <button
                type="button"
                onClick={() => setShowConfirm(v => !v)}
                className="absolute right-3 top-1/2 -translate-y-1/2 text-arcade-muted hover:text-white transition-colors"
                aria-label={showConfirm ? 'Ocultar contraseña' : 'Mostrar contraseña'}
              >
                {showConfirm ? (
                  <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                  </svg>
                ) : (
                  <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                  </svg>
                )}
              </button>
            </div>
            {passwordMismatch && (
              <p className="text-xs text-red-400">Las contraseñas no coinciden</p>
            )}
          </div>

          {/* Error */}
          {error && (
            <p className="text-red-400 text-sm bg-red-400/10 border border-red-400/20 rounded-lg px-4 py-2.5">
              {error}
            </p>
          )}

          {/* Submit */}
          <button
            type="submit"
            disabled={loading || passwordMismatch}
            className="w-full bg-arcade-purple hover:bg-purple-500 disabled:opacity-50 disabled:cursor-not-allowed text-white font-medium rounded-lg py-2.5 text-sm transition-colors flex items-center justify-center gap-2"
          >
            {loading ? (
              <>
                <span className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                Creando cuenta...
              </>
            ) : (
              'Crear cuenta'
            )}
          </button>
        </form>
      </div>
    </div>
  )
}
