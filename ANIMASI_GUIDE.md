# 🎬 Guide Animasi BelanjaIn

<div align="center">

**Dokumentasi lengkap semua animasi dan efek visual**

![Animation](https://img.shields.io/badge/Animations-50+-blue)
![CSS-Level](https://img.shields.io/badge/CSS-Advanced-green)
![Performance](https://img.shields.io/badge/Performance-Optimized-success)

</div>

---

## 🌟 Daftar Animasi

### **1. Product Card Animations**

#### **Fade In Up (Staggered)**
```css
.product-card
```
- ✨ Muncul dari bawah dengan fade
- ⏱️ Delay bertahap per card (0.1s, 0.2s, 0.3s...)
- 🎯 Auto-applied ke semua product cards

#### **Hover Effect**
- 🔼 Naik 8px ke atas
- 💫 Shadow membesar
- 🔵 Border berubah cyan
- 🖼️ Gambar zoom 1.1x dengan rotasi 2°

---

### **2. Button Animations**

#### **Primary Button**
```css
.btn-primary
```
- 🌈 Gradient bergerak (3s infinite)
- ✨ Shine effect on hover (sweep dari kiri ke kanan)
- 📏 Scale 1.05 on hover
- 💫 Shadow glow meningkat

**Effect:**
- Background gradient animasi otomatis
- Hover: cahaya putih menyapu dari kiri ke kanan
- Click: Scale down 0.95 (active state)

#### **Secondary Button**
```css
.btn-secondary
```
- 📏 Scale 1.05 on hover
- 🔵 Border cyan-600
- 💫 Shadow appear

---

### **3. Category Icons**

#### **Fade In Scale (Staggered)**
```css
.category-item
```
- 🎯 Muncul dengan scale dari 0.8 → 1
- ⏱️ Delay 0.05s per item
- 🔄 Hover: scale 1.1 + shadow
- 🎪 Hover: Bounce animation infinite

**Bounce on Hover:**
- Loncat naik-turun 10px
- Scale 1.15 di puncak
- Repeat infinite selama hover

---

### **4. Text Animations**

#### **Gradient Text**
```css
.gradient-text
```
- 🌈 3-color gradient (cyan → blue → purple)
- 🔄 Flow animation 3s infinite
- 📏 Background-size 200% untuk smooth flow

#### **Price Tag**
```css
.price-tag
```
- 💰 Sama dengan gradient text
- 🎨 3-color gradient animated
- ✨ Professional look

---

### **5. Badge Animations**

#### **Pulse Glow**
```css
.badge-animated
```
- 💫 Box-shadow glow pulse
- 🌈 Gradient background shift
- ⏱️ 2s infinite loop

**Effects:**
- Shadow: 20px → 40px (glow in-out)
- Gradient position shift
- Smooth breathing effect

#### **Store Badge Glow**
```css
.store-badge
```
- 🔥 Orange glow animation
- 💫 3-color gradient (amber → orange → red)
- ⚡ Intense glow on peak

---

### **6. Gallery Animations**

#### **3D Thumbnail**
```css
.gallery-thumb
```
- 🔄 3D perspective on hover
- 📐 Rotate 5° Y-axis
- 📏 Scale 1.1
- 🎯 Active: ring + scale effect

#### **Active Thumb Animation**
- 🔄 Rotate -5° → 5° → 0°
- 📏 Scale 0.9 → 1.15 → 1.1
- ⏱️ 0.5s smooth transition

---

### **7. Input & Form Animations**

#### **Focus Effect**
```css
.input-focus
```
- 🔵 Ring 4px cyan-300
- 🔷 Border cyan-500
- 📏 Scale 1.05
- ⏱️ 300ms smooth

**Visual:**
- Input "tumbuh" sedikit saat focus
- Ring muncul dengan fade
- Border color transition smooth

---

### **8. Glass Effect**

#### **Glassmorphism**
```css
.glass-effect
```
- 🔷 Backdrop blur 24px
- 💫 White opacity 80%
- 🎈 Float animation 4s infinite
- ⬆️ Naik-turun 5px

**Use Case:**
- Modal backgrounds
- Floating cards
- Overlay elements

---

### **9. Loading & Placeholder**

#### **Shimmer Effect**
```css
.shimmer
```
- ✨ Light sweep dari kiri ke kanan
- ⏱️ 2s infinite
- 📏 1000px gradient width
- 🎨 Gray gradient (f0 → e0 → f0)

**Perfect for:**
- Loading skeletons
- Placeholder content
- Lazy load states

---

## 🎨 Keyframe Animations

### **Available Animations:**

| Animation | Duration | Effect | Usage |
|-----------|----------|--------|-------|
| `fadeInUp` | 0.6s | Fade + slide up | Product cards, sections |
| `fadeInScale` | 0.5s | Fade + scale | Icons, badges |
| `fadeInLeft` | 0.6s | Fade + slide left | Side content |
| `fadeInRight` | 0.6s | Fade + slide right | Side content |
| `bounce` | 1s | Bounce up-down | Hover effects |
| `float` | 3s | Slow float | Decorative elements |
| `pulse-glow` | 2s | Shadow glow pulse | Badges, CTAs |
| `shimmer` | 2s | Shine sweep | Loading states |
| `gradientShift` | 3s | Gradient position | Buttons, backgrounds |
| `gradientFlow` | 3s | Gradient flow | Text, price tags |
| `glassFloat` | 4s | Gentle float | Glass elements |
| `badgeGlow` | 2s | Badge glow effect | Store badges |
| `activeThumb` | 0.5s | Scale + rotate | Gallery thumbs |
| `slideInDown` | 0.5s | Slide from top | Dropdowns, modals |
| `zoomIn` | 0.5s | Zoom from center | Popups, images |
| `rotateIn` | 0.6s | Rotate + scale | Icons, alerts |
| `heartbeat` | 1.5s | Pulsing scale | Favorite icons |

---

## 🎯 Utility Classes

### **Animation Helpers:**

```html
<!-- Fade Animations -->
<div class="animate-fade-in-up">Content</div>
<div class="animate-fade-in-left">Content</div>
<div class="animate-fade-in-right">Content</div>

<!-- Motion Animations -->
<div class="animate-bounce-in">Content</div>
<div class="animate-float">Content</div>
<div class="animate-slide-down">Content</div>

<!-- Scale Animations -->
<div class="animate-zoom-in">Content</div>
<div class="animate-rotate-in">Content</div>

<!-- Special Effects -->
<div class="animate-heartbeat">♥</div>
<div class="shimmer">Loading...</div>
<div class="float-animation">🎈</div>
```

### **Hover Utilities:**

```html
<!-- Lift on Hover -->
<div class="hover-lift">Hover me</div>

<!-- Grow on Hover -->
<button class="hover-grow">Click</button>

<!-- Rotate on Hover -->
<img class="hover-rotate" src="...">
```

---

## 🎭 Advanced Effects

### **1. Staggered Animations**

Product cards dan category icons menggunakan **staggered animation** untuk efek berurutan:

```css
.product-card:nth-child(1) { animation-delay: 0.1s; }
.product-card:nth-child(2) { animation-delay: 0.2s; }
.product-card:nth-child(3) { animation-delay: 0.3s; }
/* ... dan seterusnya */
```

**Result:** Cards muncul satu per satu dengan interval 0.1s

### **2. 3D Transforms**

Gallery thumbnails menggunakan **3D perspective**:

```css
.gallery-thumb:hover {
    transform: scale(1.1) translateZ(20px) rotateY(5deg);
}
```

**Result:** Thumbnail "keluar" dari layar dengan rotasi 3D

### **3. Pseudo-element Animations**

Button primary menggunakan **::before pseudo-element** untuk shine effect:

```css
.btn-primary::before {
    content: '';
    transform: translateX(-100%) skewX(-15deg);
}

.btn-primary:hover::before {
    transform: translateX(100%) skewX(-15deg);
}
```

**Result:** Cahaya putih menyapu dari kiri ke kanan

---

## 📊 Performance Tips

### **Optimized Animations:**

✅ **GPU-Accelerated Properties:**
- `transform` (scale, translate, rotate)
- `opacity`
- `filter` (blur, brightness)

❌ **Avoid Animating:**
- `width`, `height` (causes reflow)
- `margin`, `padding` (causes reflow)
- `top`, `left` (use `transform` instead)

### **Best Practices:**

```css
/* ✅ GOOD - GPU accelerated */
.element {
    transform: translateY(-10px);
    opacity: 0.8;
}

/* ❌ BAD - Causes reflow */
.element {
    margin-top: -10px;
    height: 150px;
}
```

### **Animation Performance:**

| Property | Performance | Alternative |
|----------|-------------|-------------|
| margin-top | 🔴 Slow | transform: translateY() |
| width/height | 🔴 Slow | transform: scale() |
| left/right | 🟡 Medium | transform: translateX() |
| transform | 🟢 Fast | ✓ Use this |
| opacity | 🟢 Fast | ✓ Use this |

---

## 🎪 Animation Timing

### **Duration Guidelines:**

| Effect Type | Duration | Easing |
|-------------|----------|--------|
| Micro interactions | 100-200ms | ease-out |
| Element transitions | 300-500ms | ease-in-out |
| Page transitions | 500-800ms | ease |
| Loading indicators | 2-3s (infinite) | linear |
| Decorative | 3-5s (infinite) | ease-in-out |

### **Delay Patterns:**

```css
/* Sequential (waterfall) */
:nth-child(1) { animation-delay: 0.1s; }
:nth-child(2) { animation-delay: 0.2s; }
:nth-child(3) { animation-delay: 0.3s; }

/* Exponential (fast start, slow end) */
:nth-child(1) { animation-delay: 0.05s; }
:nth-child(2) { animation-delay: 0.15s; }
:nth-child(3) { animation-delay: 0.35s; }
```

---

## 🔧 Customization

### **Change Animation Duration:**

```css
/* Default */
.product-card {
    animation: fadeInUp 0.6s ease-out forwards;
}

/* Faster */
.product-card {
    animation: fadeInUp 0.3s ease-out forwards;
}

/* Slower */
.product-card {
    animation: fadeInUp 1s ease-out forwards;
}
```

### **Change Animation Delay:**

```css
/* More spacing between items */
.product-card:nth-child(1) { animation-delay: 0.2s; }
.product-card:nth-child(2) { animation-delay: 0.4s; }
.product-card:nth-child(3) { animation-delay: 0.6s; }
```

### **Change Hover Intensity:**

```css
/* More dramatic hover */
.product-card:hover {
    transform: translateY(-12px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}

/* Subtle hover */
.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}
```

---

## 🎨 Color Animations

### **Gradient Combinations:**

```css
/* Cyan-Blue (Default) */
bg-gradient-to-r from-cyan-500 to-blue-600

/* Purple-Pink */
bg-gradient-to-r from-purple-500 to-pink-600

/* Green-Teal */
bg-gradient-to-r from-green-500 to-teal-600

/* Orange-Red */
bg-gradient-to-r from-orange-500 to-red-600

/* Triple Gradient (Animated) */
bg-gradient-to-r from-cyan-500 via-blue-500 to-purple-600
```

### **Glow Colors:**

```css
/* Cyan Glow */
shadow-cyan-500/30  /* 30% opacity */
shadow-cyan-500/50  /* 50% opacity */

/* Custom Glow */
box-shadow: 0 0 20px rgba(6, 182, 212, 0.5);
```

---

## 📱 Mobile Considerations

### **Reduce Animations on Mobile:**

```css
@media (max-width: 768px) {
    /* Faster animations */
    .product-card {
        animation-duration: 0.3s;
    }
    
    /* Simpler hover */
    .product-card:hover {
        transform: translateY(-4px);
    }
    
    /* Disable expensive animations */
    .gallery-thumb:hover {
        transform: scale(1.05); /* No 3D */
    }
}
```

### **Respect User Preferences:**

```css
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
```

---

## 🚀 Browser Support

### **Compatibility:**

| Feature | Chrome | Firefox | Safari | Edge |
|---------|--------|---------|--------|------|
| CSS Animations | ✅ 43+ | ✅ 16+ | ✅ 9+ | ✅ 12+ |
| CSS Transforms | ✅ 36+ | ✅ 16+ | ✅ 9+ | ✅ 12+ |
| CSS Gradients | ✅ 26+ | ✅ 16+ | ✅ 7+ | ✅ 12+ |
| Backdrop Filter | ✅ 76+ | ✅ 103+ | ✅ 9+ | ✅ 79+ |
| 3D Transforms | ✅ 36+ | ✅ 16+ | ✅ 9+ | ✅ 12+ |

### **Fallbacks:**

```css
/* Gradient fallback */
.gradient-text {
    color: #0891b2; /* Fallback */
    background: linear-gradient(...);
    background-clip: text;
}

/* Backdrop-blur fallback */
.glass-effect {
    background: rgba(255, 255, 255, 0.95); /* Fallback */
    backdrop-filter: blur(24px);
}
```

---

## 🎓 Advanced Techniques

### **1. Custom Cubic-Bezier:**

```css
/* Bounce effect */
transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);

/* Smooth ease */
transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);

/* Elastic */
transition: all 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
```

### **2. Animation Composition:**

```css
.element {
    animation: 
        fadeInUp 0.6s ease-out,
        pulse-glow 2s ease-in-out infinite;
}
```

### **3. Animation Play State:**

```css
.element {
    animation-play-state: paused;
}

.element:hover {
    animation-play-state: running;
}
```

---

## 💡 Tips & Tricks

### **1. Smooth Page Load:**

```html
<body class="opacity-0">
<script>
    window.addEventListener('load', () => {
        document.body.classList.remove('opacity-0');
        document.body.classList.add('animate-fade-in-up');
    });
</script>
```

### **2. Intersection Observer:**

```javascript
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('animate-fade-in-up');
        }
    });
});

document.querySelectorAll('.animate-on-scroll').forEach(el => {
    observer.observe(el);
});
```

### **3. Sequential Delays with JS:**

```javascript
document.querySelectorAll('.stagger-item').forEach((el, index) => {
    el.style.animationDelay = `${index * 0.1}s`;
});
```

---

## 📚 Resources

### **Learn More:**
- 🌐 [CSS Tricks - Animation](https://css-tricks.com/almanac/properties/a/animation/)
- 📖 [MDN - CSS Animations](https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_Animations)
- 🎨 [Animista](https://animista.net/) - Animation Generator
- ⚡ [Cubic-bezier.com](https://cubic-bezier.com/) - Easing Generator

### **Inspiration:**
- 🎬 [CodePen - Animations](https://codepen.io/tag/animation)
- 🌟 [Awwwards](https://www.awwwards.com/) - Web Design
- 💫 [Dribbble - Motion](https://dribbble.com/tags/motion)

---

<div align="center">

**🎉 Enjoy the Smooth Animations! 🎉**

Every interaction is now delightful and professional!

Made with ❤️ by BelanjaIn Team

[🏠 Back to README](README.md) | [📸 Photo Guide](PANDUAN_UPLOAD_FOTO.md) | [🚀 Quick Start](QUICK_START.md)

</div>
