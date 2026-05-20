const API_URL = import.meta.env.VITE_API_URL

export const API_ROUTES = {
  GAMES: {
    ALL: `${API_URL}/juegos`,
    POPULAR: `${API_URL}/juegos/populares`,
    RECENT: `${API_URL}/juegos/recientes`,
    DETAIL: (id) => `${API_URL}/juegos/${id}`,
    SEARCH: `${API_URL}/juegos/buscar`,
  },

  SCORES: {
    ALL: `${API_URL}/puntuaciones`,
    BY_GAME: (gameId) => `${API_URL}/puntuaciones/juego/${gameId}`,
    MY_BY_GAME: (gameId) => `${API_URL}/puntuaciones/me/juego/${gameId}`,
  },

  AUTH: {
    LOGIN: `${API_URL}/login`,
    REGISTER: `${API_URL}/register`,
    LOGOUT: `${API_URL}/logout`,
    PROFILE: `${API_URL}/account`,
  },

  FRIENDS: {
    LIST: `${API_URL}/amigos`,
    PENDING: `${API_URL}/amigos/solicitudes`,
    REQUEST: `${API_URL}/amigos/solicitud`,
    ACCEPT: (id) => `${API_URL}/amigos/${id}/aceptar`,
    REJECT: (id) => `${API_URL}/amigos/${id}/rechazar`,
    REMOVE: (id) => `${API_URL}/amigos/${id}`,
  },

  USERS: {
    PROFILE: (id) => `${API_URL}/usuarios/${id}`,
    UPDATE: `${API_URL}/usuarios/perfil`,
  },

  ADMIN: {
    USERS: {
      LIST: `${API_URL}/users`,
      DETAIL: (id) => `${API_URL}/users/${id}`,
      CREATE: `${API_URL}/users`,
      UPDATE: (id) => `${API_URL}/users/${id}`,
      DELETE: (id) => `${API_URL}/users/${id}`,
    },
    JUEGOS: {
      CREATE: `${API_URL}/juegos`,
      UPDATE: (id) => `${API_URL}/juegos/${id}`,
      DELETE: (id) => `${API_URL}/juegos/${id}`,
    },
    LOGROS: {
      LIST: `${API_URL}/logros`,
      DETAIL: (id) => `${API_URL}/logros/${id}`,
      CREATE: `${API_URL}/logros`,
      UPDATE: (id) => `${API_URL}/logros/${id}`,
      DELETE: (id) => `${API_URL}/logros/${id}`,
    },
    AMIGOS: {
      LIST: `${API_URL}/amigos`,
      DETAIL: (id) => `${API_URL}/amigos/${id}`,
      CREATE: `${API_URL}/amigos`,
      UPDATE: (id) => `${API_URL}/amigos/${id}`,
      DELETE: (id) => `${API_URL}/amigos/${id}`,
    },
    PUNTUACIONES: {
      DETAIL: (id) => `${API_URL}/puntuaciones/${id}`,
      CREATE: `${API_URL}/puntuaciones`,
      UPDATE: (id) => `${API_URL}/puntuaciones/${id}`,
      DELETE: (id) => `${API_URL}/puntuaciones/${id}`,
    },
    RESULTADOS: {
      LIST: `${API_URL}/resultados`,
      DETAIL: (id) => `${API_URL}/resultados/${id}`,
      CREATE: `${API_URL}/resultados`,
      UPDATE: (id) => `${API_URL}/resultados/${id}`,
      DELETE: (id) => `${API_URL}/resultados/${id}`,
    },
  },
}
