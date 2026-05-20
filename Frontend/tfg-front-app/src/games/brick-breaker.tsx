import { useEffect, useRef } from 'react'
import type { GameProps } from './registry'
import { SCORE_STORAGE_KEY } from './registry'

// ─── Canvas ───────────────────────────────────────────────────────────────────
const CW = 800
const CH = 520

// ─── Paddle ───────────────────────────────────────────────────────────────────
const PW = 110
const PH = 12
const PY = CH - 40

// ─── Ball ─────────────────────────────────────────────────────────────────────
const BR = 7
const BALL_SPEED = 4.8 // px per frame at 60 fps

// ─── Bricks ───────────────────────────────────────────────────────────────────
const COLS = 10
const ROWS = 5
const BW = 68
const BH = 22
const H_GAP = 8
const V_GAP = 6
const BX0 = (CW - COLS * BW - (COLS - 1) * H_GAP) / 2
const BY0 = 55

// Row 0 = top row (easiest), Row 4 = bottom row (hardest)
const ROW_CFG = [
  { maxHp: 1, color: '#60a5fa', hitColor: '#1d4ed8' }, // blue   – 1 HP
  { maxHp: 1, color: '#c084fc', hitColor: '#7e22ce' }, // purple – 1 HP
  { maxHp: 2, color: '#4ade80', hitColor: '#15803d' }, // green  – 2 HP
  { maxHp: 3, color: '#fb923c', hitColor: '#c2410c' }, // orange – 3 HP
  { maxHp: 4, color: '#f87171', hitColor: '#991b1b' }, // red    – 4 HP
] as const

type Phase = 'idle' | 'playing' | 'gameover' | 'won'

interface Brick {
  x: number; y: number
  hp: number; maxHp: number
  color: string; hitColor: string
}

export default function BrickBreaker({ gameId }: GameProps) {
  const canvasRef = useRef<HTMLCanvasElement>(null)

  useEffect(() => {
    const canvas = canvasRef.current
    if (!canvas) return
    const ctx = canvas.getContext('2d')!

    // ── State ─────────────────────────────────────────────────────────────────
    let phase: Phase = 'idle'
    let rafId = 0
    let prevTs = 0

    let px = CW / 2            // paddle center x
    let bx = CW / 2            // ball x
    let by = PY - BR - 2       // ball y
    let vx = 0; let vy = 0
    let score = 0
    let bricks: Brick[] = []
    const keys = new Set<string>()
    let mouseX = -1

    // ── Helpers ───────────────────────────────────────────────────────────────
    function rr(x: number, y: number, w: number, h: number, r: number) {
      ctx.beginPath()
      ctx.moveTo(x + r, y)
      ctx.lineTo(x + w - r, y)
      ctx.arcTo(x + w, y, x + w, y + r, r)
      ctx.lineTo(x + w, y + h - r)
      ctx.arcTo(x + w, y + h, x + w - r, y + h, r)
      ctx.lineTo(x + r, y + h)
      ctx.arcTo(x, y + h, x, y + h - r, r)
      ctx.lineTo(x, y + r)
      ctx.arcTo(x, y, x + r, y, r)
      ctx.closePath()
    }

    function makeBricks() {
      bricks = []
      for (let row = 0; row < ROWS; row++) {
        const cfg = ROW_CFG[row]
        for (let col = 0; col < COLS; col++) {
          bricks.push({
            x: BX0 + col * (BW + H_GAP),
            y: BY0 + row * (BH + V_GAP),
            hp: cfg.maxHp, maxHp: cfg.maxHp,
            color: cfg.color, hitColor: cfg.hitColor,
          })
        }
      }
    }

    function launchBall() {
      bx = px
      by = PY - BR - 2
      const a = (-55 + Math.random() * 110) * (Math.PI / 180)
      vx = BALL_SPEED * Math.sin(a)
      vy = -Math.abs(BALL_SPEED * Math.cos(a))
    }

    function endGame(result: 'gameover' | 'won') {
      phase = result
      cancelAnimationFrame(rafId)
      localStorage.setItem(SCORE_STORAGE_KEY(gameId), JSON.stringify({ score }))
      window.dispatchEvent(new Event('classicgames:score'))
      draw()
    }

    function startGame() {
      phase = 'playing'
      score = 0
      px = CW / 2
      makeBricks()
      launchBall()
      prevTs = performance.now()
      cancelAnimationFrame(rafId)
      rafId = requestAnimationFrame(tick)
    }

    // ── Draw ──────────────────────────────────────────────────────────────────
    function draw() {
      // Background
      ctx.fillStyle = '#0d0b1a'
      ctx.fillRect(0, 0, CW, CH)

      // Subtle grid
      ctx.strokeStyle = 'rgba(255,255,255,0.025)'
      ctx.lineWidth = 1
      for (let x = 0; x <= CW; x += 40) {
        ctx.beginPath(); ctx.moveTo(x, 0); ctx.lineTo(x, CH); ctx.stroke()
      }
      for (let y = 0; y <= CH; y += 40) {
        ctx.beginPath(); ctx.moveTo(0, y); ctx.lineTo(CW, y); ctx.stroke()
      }

      // ── Bricks ──
      for (const b of bricks) {
        if (b.hp <= 0) continue

        const dmgRatio = 1 - b.hp / b.maxHp
        const col = dmgRatio > 0.01 ? b.hitColor : b.color

        ctx.shadowColor = col
        ctx.shadowBlur = 6
        ctx.fillStyle = col
        rr(b.x, b.y, BW, BH, 3)
        ctx.fill()
        ctx.shadowBlur = 0

        // Shine
        ctx.fillStyle = 'rgba(255,255,255,0.18)'
        rr(b.x + 2, b.y + 2, BW - 4, BH / 2 - 2, 2)
        ctx.fill()

        // Damage overlay
        if (dmgRatio > 0) {
          ctx.fillStyle = `rgba(0,0,0,${dmgRatio * 0.45})`
          rr(b.x, b.y, BW, BH, 3)
          ctx.fill()
        }

        // HP dots for multi-hit bricks
        if (b.maxHp > 1) {
          const sp = 9
          const sx = b.x + BW / 2 - ((b.hp - 1) * sp) / 2
          for (let i = 0; i < b.hp; i++) {
            ctx.fillStyle = 'rgba(255,255,255,0.8)'
            ctx.beginPath()
            ctx.arc(sx + i * sp, b.y + BH - 5, 2.5, 0, Math.PI * 2)
            ctx.fill()
          }
        }
      }

      // ── Paddle ──
      const pg = ctx.createLinearGradient(px - PW / 2, PY, px + PW / 2, PY)
      pg.addColorStop(0, '#4c1d95')
      pg.addColorStop(0.5, '#a78bfa')
      pg.addColorStop(1, '#4c1d95')
      ctx.fillStyle = pg
      ctx.shadowColor = '#a78bfa'
      ctx.shadowBlur = 18
      rr(px - PW / 2, PY, PW, PH, 6)
      ctx.fill()
      ctx.shadowBlur = 0

      // ── Ball ──
      if (phase !== 'gameover') {
        const bg = ctx.createRadialGradient(bx - 2, by - 2, 1, bx, by, BR)
        bg.addColorStop(0, '#ffffff')
        bg.addColorStop(0.4, '#ddd6fe')
        bg.addColorStop(1, '#7c3aed')
        ctx.fillStyle = bg
        ctx.shadowColor = '#c4b5fd'
        ctx.shadowBlur = 18
        ctx.beginPath()
        ctx.arc(bx, by, BR, 0, Math.PI * 2)
        ctx.fill()
        ctx.shadowBlur = 0
      }

      // ── Score HUD ──
      ctx.fillStyle = 'rgba(255,255,255,0.65)'
      ctx.font = 'bold 14px "Courier New", monospace'
      ctx.textAlign = 'right'
      ctx.fillText(`SCORE  ${score.toLocaleString()}`, CW - 14, 26)
      ctx.textAlign = 'left'

      // ── Overlay ──
      if (phase !== 'playing') drawOverlay()
    }

    function drawOverlay() {
      ctx.fillStyle = 'rgba(13,11,26,0.82)'
      ctx.fillRect(0, 0, CW, CH)
      ctx.textAlign = 'center'

      if (phase === 'idle') {
        ctx.font = 'bold 42px "Courier New", monospace'
        ctx.fillStyle = '#ffffff'
        ctx.fillText('BRICK BREAKER', CW / 2, CH / 2 - 42)
        ctx.font = '16px "Courier New", monospace'
        ctx.fillStyle = '#a78bfa'
        ctx.fillText('Toca la pantalla o pulsa ENTER para comenzar', CW / 2, CH / 2 + 8)
        ctx.font = '13px "Courier New", monospace'
        ctx.fillStyle = 'rgba(255,255,255,0.35)'
        ctx.fillText('← →  /  A D  /  Ratón  /  Arrastra en móvil', CW / 2, CH / 2 + 40)
      } else if (phase === 'gameover') {
        ctx.font = 'bold 52px "Courier New", monospace'
        ctx.fillStyle = '#f87171'
        ctx.fillText('GAME OVER', CW / 2, CH / 2 - 28)
        ctx.font = '18px "Courier New", monospace'
        ctx.fillStyle = 'rgba(255,255,255,0.85)'
        ctx.fillText(`Puntuación final: ${score.toLocaleString()}`, CW / 2, CH / 2 + 18)
        ctx.font = '13px "Courier New", monospace'
        ctx.fillStyle = 'rgba(255,255,255,0.4)'
        ctx.fillText('ENTER, clic o toca para reiniciar', CW / 2, CH / 2 + 52)
      } else {
        ctx.font = 'bold 52px "Courier New", monospace'
        ctx.fillStyle = '#4ade80'
        ctx.fillText('¡VICTORIA!', CW / 2, CH / 2 - 28)
        ctx.font = '18px "Courier New", monospace'
        ctx.fillStyle = 'rgba(255,255,255,0.85)'
        ctx.fillText(`Puntuación final: ${score.toLocaleString()}`, CW / 2, CH / 2 + 18)
        ctx.font = '13px "Courier New", monospace'
        ctx.fillStyle = 'rgba(255,255,255,0.4)'
        ctx.fillText('ENTER, clic o toca para reiniciar', CW / 2, CH / 2 + 52)
      }

      ctx.textAlign = 'left'
    }

    // ── Game loop ─────────────────────────────────────────────────────────────
    function tick(ts: number) {
      const dt = Math.min((ts - prevTs) / 16.667, 3)
      prevTs = ts

      // ── Paddle ──
      if (mouseX >= 0) {
        px = Math.max(PW / 2, Math.min(CW - PW / 2, mouseX))
      }
      if (keys.has('ArrowLeft') || keys.has('KeyA')) px = Math.max(PW / 2, px - 10 * dt)
      if (keys.has('ArrowRight') || keys.has('KeyD')) px = Math.min(CW - PW / 2, px + 10 * dt)

      // ── Ball movement ──
      bx += vx * dt
      by += vy * dt

      // Wall bounces
      if (bx - BR <= 0) { bx = BR; vx = Math.abs(vx) }
      if (bx + BR >= CW) { bx = CW - BR; vx = -Math.abs(vx) }
      if (by - BR <= 0) { by = BR; vy = Math.abs(vy) }

      // Bottom → game over
      if (by - BR > CH) { endGame('gameover'); return }

      // ── Paddle collision ──
      const pl = px - PW / 2
      const pr = px + PW / 2
      if (vy > 0 && by + BR >= PY && by < PY + PH + 6 && bx >= pl - 4 && bx <= pr + 4) {
        const hitPos = Math.max(-1, Math.min(1, (bx - px) / (PW / 2)))
        const angle = hitPos * 65 * (Math.PI / 180)
        const spd = Math.hypot(vx, vy)
        vx = spd * Math.sin(angle)
        vy = -Math.abs(spd * Math.cos(angle))
        by = PY - BR
      }

      // ── Brick collisions ──
      for (const b of bricks) {
        if (b.hp <= 0) continue

        // AABB quick reject
        if (bx + BR <= b.x || bx - BR >= b.x + BW ||
          by + BR <= b.y || by - BR >= b.y + BH) continue

        // Minimum overlap → bounce axis
        const ol = bx + BR - b.x          // overlap left side of brick
        const or_ = b.x + BW - (bx - BR)  // overlap right side
        const ot = by + BR - b.y          // overlap top side
        const ob = b.y + BH - (by - BR)   // overlap bottom side
        const mn = Math.min(ol, or_, ot, ob)

        if (mn === ot || mn === ob) {
          vy = -vy
          by = mn === ot ? b.y - BR : b.y + BH + BR
        } else {
          vx = -vx
          bx = mn === ol ? b.x - BR : b.x + BW + BR
        }

        // Damage & score
        b.hp--
        score += b.hp <= 0 ? 1000 : 100

        // Win check
        if (bricks.every(b => b.hp <= 0)) { endGame('won'); return }
        break // one brick per frame
      }

      draw()
      rafId = requestAnimationFrame(tick)
    }

    // ── Events ────────────────────────────────────────────────────────────────
    const onKeyDown = (e: KeyboardEvent) => {
      if (['ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Space'].includes(e.key)) e.preventDefault()
      keys.add(e.code)
      if (e.code === 'Enter' && phase !== 'playing') startGame()
    }
    const onKeyUp = (e: KeyboardEvent) => keys.delete(e.code)

    const onMouseMove = (e: MouseEvent) => {
      const rect = canvas.getBoundingClientRect()
      mouseX = (e.clientX - rect.left) * (CW / rect.width)
    }
    const onMouseLeave = () => { mouseX = -1 }
    const onClick = () => { if (phase !== 'playing') startGame() }

    // ── Touch events ──────────────────────────────────────────────────────────
    function touchCanvasX(touch: Touch): number {
      const rect = canvas!.getBoundingClientRect()
      return (touch.clientX - rect.left) * (CW / rect.width)
    }

    const onTouchStart = (e: TouchEvent) => {
      e.preventDefault() // prevent scroll & 300 ms click delay
      const x = touchCanvasX(e.touches[0])
      mouseX = x
      if (phase !== 'playing') startGame()
    }

    const onTouchMove = (e: TouchEvent) => {
      e.preventDefault()
      mouseX = touchCanvasX(e.touches[0])
    }

    const onTouchEnd = () => {
      mouseX = -1
    }

    window.addEventListener('keydown', onKeyDown)
    window.addEventListener('keyup', onKeyUp)
    canvas.addEventListener('mousemove', onMouseMove)
    canvas.addEventListener('mouseleave', onMouseLeave)
    canvas.addEventListener('click', onClick)
    canvas.addEventListener('touchstart', onTouchStart, { passive: false })
    canvas.addEventListener('touchmove',  onTouchMove,  { passive: false })
    canvas.addEventListener('touchend',   onTouchEnd)
    canvas.addEventListener('touchcancel', onTouchEnd)

    // First render (idle screen)
    makeBricks()
    draw()

    return () => {
      cancelAnimationFrame(rafId)
      window.removeEventListener('keydown', onKeyDown)
      window.removeEventListener('keyup', onKeyUp)
      canvas.removeEventListener('mousemove', onMouseMove)
      canvas.removeEventListener('mouseleave', onMouseLeave)
      canvas.removeEventListener('click', onClick)
      canvas.removeEventListener('touchstart', onTouchStart)
      canvas.removeEventListener('touchmove',  onTouchMove)
      canvas.removeEventListener('touchend',   onTouchEnd)
      canvas.removeEventListener('touchcancel', onTouchEnd)
    }
  }, [gameId])

  return (
    <canvas
      ref={canvasRef}
      width={CW}
      height={CH}
      className="w-full h-full block cursor-none"
      style={{ touchAction: 'none' }}
    />
  )
}
