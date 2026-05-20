/**
 * Convierte errores de red y códigos HTTP en mensajes legibles en español.
 * Acepta tanto un Error/unknown (desde catch) como un string ya procesado.
 */

const HTTP_MESSAGES: Record<number, string> = {
  400: 'La solicitud no es válida.',
  401: 'Sesión expirada. Vuelve a iniciar sesión.',
  403: 'No tienes permiso para acceder a este recurso.',
  404: 'El recurso solicitado no existe.',
  408: 'La conexión tardó demasiado. Inténtalo de nuevo.',
  409: 'Conflicto con los datos actuales.',
  422: 'Los datos enviados no son válidos.',
  429: 'Demasiadas solicitudes. Espera un momento.',
  500: 'Error interno del servidor. Inténtalo más tarde.',
  502: 'El servidor no está disponible en este momento. Inténtalo más tarde.',
  503: 'Servicio temporalmente fuera de servicio. Inténtalo más tarde.',
  504: 'El servidor tardó demasiado en responder. Inténtalo más tarde.',
}

const NETWORK_KEYWORDS = [
  'failed to fetch',
  'networkerror',
  'network request failed',
  'load failed',
  'err_connection_refused',
  'err_network',
  'fetch failed',
]

/** Dado un código de estado HTTP, devuelve un mensaje amigable. */
export function httpErrorMessage(status: number): string {
  return HTTP_MESSAGES[status] ?? `Error del servidor (${status}). Inténtalo más tarde.`
}

/** Dado un Error o string capturado en un catch, devuelve un mensaje amigable. */
export function friendlyError(err: unknown): string {
  const raw = err instanceof Error ? err.message : String(err)
  return humanizeMessage(raw)
}

/**
 * Dado un string (ya sea `err.message` o un mensaje almacenado en estado),
 * devuelve la versión legible. Útil para traducir mensajes ya guardados en el estado.
 */
export function humanizeMessage(raw: string): string {
  if (!raw) return 'Algo salió mal. Inténtalo más tarde.'

  // Errores de red ("Failed to fetch", "NetworkError", etc.)
  const lower = raw.toLowerCase()
  if (NETWORK_KEYWORDS.some(kw => lower.includes(kw))) {
    return 'No se puede conectar al servidor. Comprueba que el servidor esté en marcha.'
  }

  // Mensajes del patrón "Error 502" o "Error 502: Bad Gateway"
  const match = raw.match(/\bError\s+(\d{3})\b/i)
  if (match) {
    const status = parseInt(match[1], 10)
    return HTTP_MESSAGES[status] ?? `Error del servidor (${status}). Inténtalo más tarde.`
  }

  return raw
}
