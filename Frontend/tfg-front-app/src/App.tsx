import { Routes, Route } from 'react-router-dom'
import { RootLayout } from './views/RootLayout'
import { Home } from './views/home/Home'
import { Login } from './views/auth/Login'
import { Register } from './views/auth/Register'
import { Scores } from './views/scores/Scores'
import { Games } from './views/games/Games'
import { GameDetail } from './views/games/GameDetail'
import { GameLogros } from './views/games/GameLogros'
import { GameMisPuntuaciones } from './views/games/GameMisPuntuaciones'
import { Profile } from './views/profile/Profile'
import { Friends } from './views/friends/Friends'
import { PlayerPuntuaciones } from './views/friends/PlayerPuntuaciones'
import { ProtectedRoute } from './components/ProtectedRoute'
import { AdminRoute } from './components/AdminRoute'
import { AdminLayout } from './views/admin/AdminLayout'
import { AdminDashboard } from './views/admin/AdminDashboard'
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
        <Route path="juegos/:id/logros" element={<GameLogros />} />
        <Route path="juegos/:id/mis-puntuaciones" element={<GameMisPuntuaciones />} />
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
        <Route path="jugadores/:userId/puntuaciones" element={<PlayerPuntuaciones />} />
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
        <Route index element={<AdminDashboard />} />
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
