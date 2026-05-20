import type { ReactNode } from 'react'
import ReactPaginateLib from 'react-paginate'

const ReactPaginate = ((ReactPaginateLib as any).default ?? ReactPaginateLib) as typeof ReactPaginateLib

// ─── Column definition ────────────────────────────────────────────────────────

export interface Column<T> {
  header: string
  key: string
  render?: (row: T) => ReactNode
  width?: string
}

// ─── CrudPage ─────────────────────────────────────────────────────────────────

interface CrudPageProps<T> {
  title: string
  items: T[]
  columns: Column<T>[]
  page: number
  lastPage: number
  loading: boolean
  error: string | null
  onPageChange: (p: number) => void
  onNew: () => void
  onEdit: (row: T) => void
  onDelete: (row: T) => void
  deleteConfirmId?: number | null
  onDeleteConfirm: (row: T) => void
  onDeleteCancel: () => void
}

export function CrudPage<T extends { id: number }>({
  title,
  items,
  columns,
  page,
  lastPage,
  loading,
  error,
  onPageChange,
  onNew,
  onEdit,
  onDelete,
  deleteConfirmId,
  onDeleteConfirm,
  onDeleteCancel,
}: CrudPageProps<T>) {
  return (
    <div className="p-8">
      {/* Header */}
      <div className="flex items-center justify-between mb-6">
        <h1 className="text-2xl font-bold text-gray-100">{title}</h1>
        <button
          onClick={onNew}
          className="px-4 py-2 bg-orange-500 hover:bg-orange-400 text-white text-sm font-medium rounded transition-colors"
        >
          + Crear
        </button>
      </div>

      {/* Error */}
      {error && (
        <div className="mb-4 px-4 py-3 bg-red-900/40 border border-red-700 text-red-300 rounded text-sm">
          {error}
        </div>
      )}

      {/* Table */}
      <div className="bg-gray-900 border border-gray-800 rounded-lg overflow-hidden">
        {loading ? (
          <div className="flex items-center justify-center h-48">
            <div className="w-7 h-7 border-2 border-orange-500 border-t-transparent rounded-full animate-spin" />
          </div>
        ) : items.length === 0 ? (
          <p className="text-gray-500 text-center py-12 text-sm">Sin registros</p>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-sm">
              <thead>
                <tr className="border-b border-gray-800 text-gray-400 text-xs uppercase tracking-wider">
                  {columns.map((col) => (
                    <th
                      key={col.key}
                      className={`px-4 py-3 text-left font-medium ${col.width ?? ''}`}
                    >
                      {col.header}
                    </th>
                  ))}
                  <th className="px-4 py-3 text-right font-medium w-24">Acciones</th>
                </tr>
              </thead>
              <tbody>
                {items.map((row) => (
                  <tr
                    key={row.id}
                    className="border-b border-gray-800/50 hover:bg-gray-800/40 transition-colors"
                  >
                    {columns.map((col) => (
                      <td key={col.key} className="px-4 py-3 text-gray-300">
                        {col.render ? col.render(row) : String((row as Record<string, unknown>)[col.key] ?? '—')}
                      </td>
                    ))}
                    <td className="px-4 py-3 text-right">
                      {deleteConfirmId === row.id ? (
                        <span className="inline-flex items-center gap-2">
                          <button
                            onClick={() => onDeleteConfirm(row)}
                            className="text-xs text-red-400 hover:text-red-300 font-medium"
                          >
                            Confirmar
                          </button>
                          <button
                            onClick={onDeleteCancel}
                            className="text-xs text-gray-500 hover:text-gray-300"
                          >
                            Cancelar
                          </button>
                        </span>
                      ) : (
                        <span className="inline-flex items-center gap-3">
                          <button
                            onClick={() => onEdit(row)}
                            className="text-xs text-orange-400 hover:text-orange-300 font-medium"
                          >
                            Editar
                          </button>
                          <button
                            onClick={() => onDelete(row)}
                            className="text-xs text-red-500 hover:text-red-400"
                          >
                            Eliminar
                          </button>
                        </span>
                      )}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>

      {/* Pagination */}
      {lastPage > 1 && (
        <div className="mt-4 flex justify-center">
          <ReactPaginate
            pageCount={lastPage}
            forcePage={page - 1}
            onPageChange={({ selected }) => onPageChange(selected + 1)}
            previousLabel="‹"
            nextLabel="›"
            breakLabel="…"
            pageRangeDisplayed={3}
            marginPagesDisplayed={1}
            containerClassName="flex items-center gap-1 text-sm"
            pageClassName="rounded"
            pageLinkClassName="px-3 py-1 rounded text-gray-400 hover:text-gray-100 hover:bg-gray-800 block transition-colors"
            activeClassName="!text-orange-400"
            activeLinkClassName="bg-orange-500/20 !text-orange-400"
            previousLinkClassName="px-3 py-1 rounded text-gray-400 hover:text-gray-100 hover:bg-gray-800 block transition-colors"
            nextLinkClassName="px-3 py-1 rounded text-gray-400 hover:text-gray-100 hover:bg-gray-800 block transition-colors"
            disabledLinkClassName="opacity-30 cursor-default"
          />
        </div>
      )}
    </div>
  )
}
