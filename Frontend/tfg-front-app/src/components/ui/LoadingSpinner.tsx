export function LoadingSpinner() {
  return (
    <div className="flex justify-center items-center py-16">
      <div className="w-10 h-10 border-2 border-arcade-border border-t-arcade-purple rounded-full animate-spin" />
    </div>
  )
}

export function GameCardSkeleton() {
  return (
    <div className="bg-arcade-card rounded-lg overflow-hidden animate-pulse">
      <div className="bg-arcade-border aspect-video w-full" />
      <div className="p-3 space-y-2">
        <div className="h-4 bg-arcade-border rounded w-3/4" />
        <div className="h-3 bg-arcade-border rounded w-1/2" />
      </div>
    </div>
  )
}
