# Mesin Menu Hover Behavior

## Visual Feedback on Cursor Hover

When you hover over a mesin menu card, the following changes occur:

### 🎨 Card Level Changes
- **Background Color**: `#fff` → `#f8f9ff` (light blue tint)
- **Border Color**: `#e3e6f0` → `#4e73df` (blue accent)
- **Border Width**: `1px` → `2px` (slightly thicker)
- **Box Shadow**: `none` → `0 0.5rem 1.5rem rgba(78, 115, 223, 0.25)` (blue shadow)
- **Elevation**: Moves up 3px (`translateY(-3px)`)
- **Cursor**: Changes to `pointer` (hand icon)

### 🎯 Icon Changes (on hover)
- **Background**: `#f0f1f6` → `#4e73df` (blue fill)
- **Color**: `#4e73df` → `#fff` (white icon)
- **Scale**: Grows slightly (`scale(1.1)`)
- **Transition**: Smooth 0.25s ease

### 📝 Title Changes (on hover)
- **Main Title Color**: `#3a3b45` → `#4e73df` (dark gray → blue)
- **Subtitle Color**: `#858796` → `#4e73df` (gray → blue)
- **Transition**: Smooth 0.25s ease

### 📊 Data Values Changes (on hover)
- **Text Color**: `#3a3b45` → `#4e73df` (dark gray → blue)
- **Transition**: Smooth 0.25s ease

## Active State (Current Tab)

When a mesin menu is the currently active tab:

- **Background**: `linear-gradient(135deg, #f5f7ff 0%, #eaecf4 100%)` (gradient blue)
- **Border Color**: `#4e73df`
- **Border Width**: `2px`
- **Icon Background**: `#4e73df`
- **Icon Color**: `#fff`

## Interactive Sequence

```
Default State → Hover over card → 
  - Cursor becomes pointer hand ✋
  - Background tints blue
  - Border turns blue and thickens
  - Icon fills with blue and grows
  - All text turns blue
  - Card elevates up slightly
  - Blue shadow appears
  
Click → Navigate to mesin tab with smooth AJAX load
```

## CSS Transitions

All hover effects use `transition: all 0.25s ease;` for smooth, responsive feedback:
- Fast enough to feel immediate (250ms)
- Smooth easing for polished feel
- Applied to all properties for cohesive animation

## Mobile/Touch Behavior

On touch devices, the hover states persist briefly after selection or use `:active` states for feedback.

---

**File**: `/app/Views/ppic/partials/mesin_menu_list.php`  
**Modified**: Enhanced hover interactions for better UX clarity
