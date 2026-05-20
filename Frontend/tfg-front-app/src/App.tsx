import { Routes, Route, Navigate } from 'react-router-dom'
import { RootLayout } from './views/RootLayout'
import { Home } from './views/home/Home'
import { Login } from './views/auth/Login'
import { Register } from './views/auth/Register'
import { Scores } from './views/scores/Scores'
import { Games } from './views/games/Games'
import { GameDetail } from './views/games/GameDetail'
import { Profile } from './views/profile/Profile'
import { Friends } from './views/friends/Friends'
import { ProtectedRoute } from './components/ProtectedRoute'
import { AdminRoute } from './components/AdminRoute'
import { AdminLayout } from './views/admin/AdminLayout'
import { AdminUsers } from './views/admin/AdminUsers'
import { AdminJuegos } from './views/admin/AdminJuegos'
import { AdminLogros } from './views/admin/AdminLogros'
import { AdminAmigos } from './views/admin/AdminAmigos'
import { AdminPuntuaciones } from './views/admin/AdminPuntuaciones'
import { AdminResultados } from './views/admin/AdminResultados'

function App() {
  return (
    <Routes>
      {/* Public site */}
      <Route path="/" element={<RootLayout />}>
        <Route index element={<Home />} />
        <Route path="puntuaciones" element={<Scores />} />
        <Route path="juegos" element={<Games />} />
        <Route path="juegos/:id" element={<GameDetail />} />
        <Route
          path="perfil"
          element={
            <ProtectedRoute>
              <Profile />
            </ProtectedRoute>
          }
        />
        <Route
          path="amigos"
          element={
            <ProtectedRoute>
              <Friends />
            </ProtectedRoute>
          }
        />
      </Route>

      {/* Auth */}
      <Route path="/login" element={<Login />} />
      <Route path="/registro" element={<Register />} />

      {/* Admin panel */}
      <Route
        path="/admin"
        element={
          <AdminRoute>
            <AdminLayout />
          </AdminRoute>
        }
      >
        <Route index element={<Navigate to="usuarios" replace />} />
        <Route path="usuarios" element={<AdminUsers />} />
        <Route path="juegos" element={<AdminJuegos />} />
        <Route path="logros" element={<AdminLogros />} />
        <Route path="amigos" element={<AdminAmigos />} />
        <Route path="puntuaciones" element={<AdminPuntuaciones />} />
        <Route path="resultados" element={<AdminResultados />} />
      </Route>
    </Routes>
  )
}

export default App
