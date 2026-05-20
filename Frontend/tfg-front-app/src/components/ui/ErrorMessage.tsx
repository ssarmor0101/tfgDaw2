interface ErrorMessageProps {
  message: string
  onRetry?: () => void
}

export function ErrorMessage({ message, onRetry }: ErrorMessageProps) {
  return (
    <div className="flex flex-col items-center justify-center py-16 gap-4">
      <span className="text-red-400 text-sm">{message}</span>
      {onRetry && (
        <button
          onClick={onRetry}
          className="px-4 py-2 text-sm bg-arcade-surface border border-arcade-border rounded hover:border-arcade-purple transition-colors text-white"
        >
          Reintentar
        </button>
      )}
    </div>
  )
}
