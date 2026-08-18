# 🎢 Horizontal Scroll Carousel Guide

<div align="center">

**Smooth horizontal scrolling carousel untuk produk**

![Smooth](https://img.shields.io/badge/Animation-Ultra_Smooth-success)
![Touch](https://img.shields.io/badge/Touch-Enabled-blue)
![Performance](https://img.shields.io/badge/Performance-60fps-green)

</div>

---

## ✨ Features

- 🎨 **Ultra Smooth Animation** - Cubic bezier easing untuk scroll halus
- 👆 **Touch Enabled** - Swipe di mobile & desktop
- 🖱️ **Mouse Drag** - Drag dengan mouse seperti touch
- ⌨️ **Keyboard Support** - Arrow keys untuk navigate
- 📱 **Responsive** - Otomatis adapt di semua ukuran layar
- ⚡ **60fps Performance** - Menggunakan requestAnimationFrame
- 🎯 **Snap Points** - Snap ke item terdekat
- 🔘 **Navigation Arrows** - Auto hide saat di ujung
- 📊 **Auto Width Detection** - Menghitung ukuran item otomatis

---

## 🚀 Cara Penggunaan

### **Basic Implementation:**

```html
<!-- Carousel Container -->
<div data-carousel class="relative">
    <!-- Previous Button -->
    <button data-carousel-prev class="scroll-arrow scroll-arrow-left">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>

    <!-- Scroll Container -->
    <div data-carousel-container class="scroll-container">
        <!-- Items -->
        <div class="product-card w-64">Product 1</div>
        <div class="product-card w-64">Product 2</div>
        <div class="product-card w-64">Product 3</div>
        <!-- ... more items -->
    </div>

    <!-- Next Button -->
    <button data-carousel-next class="scroll-arrow scroll-arrow-right">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
        </svg>
    </button>
</div>
```

### **Attributes:**

| Attribute | Required | Description |
|-----------|----------|-------------|
| `data-carousel` | ✅ Yes | Wrapper container |
| `data-carousel-container` | ✅ Yes | Scrollable container |
| `data-carousel-prev` | ⚪ Optional | Previous button |
| `data-carousel-next` | ⚪ Optional | Next button |

---

## 🎨 Styling

### **Container Styles:**

```css
.scroll-container {
    display: flex;
    gap: 1rem;
    overflow-x: auto;
    padding-bottom: 1rem;
    scroll-snap-type: x mandatory;
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
}

.scroll-container > * {
    scroll-snap-align: center;
    flex-shrink: 0;
}
```

### **Arrow Styles:**

```css
.scroll-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    width: 3rem;
    height: 3rem;
    border-radius: 9999px;
    background: white;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgb(8, 145, 178);
    transition: all 0.3s;
    cursor: pointer;
    opacity: 0;
    animation: fadeIn 0.3s ease-out forwards;
}

.scroll-arrow:hover {
    background: rgb(236, 254, 255);
    transform: translateY(-50%) scale(1.1);
}

.scroll-arrow-left {
    left: 0.5rem;
}

.scroll-arrow-right {
    right: 0.5rem;
}
```

---

## 🎯 Examples

### **1. Flash Sale Carousel:**

```html
<section class="max-w-7xl mx-auto px-4 mt-8">
    <h2 class="text-2xl font-bold mb-4">⚡ Flash Sale</h2>
    
    <div data-carousel class="relative">
        <button data-carousel-prev class="scroll-arrow scroll-arrow-left">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        <div data-carousel-container class="scroll-container">
            @foreach($flashSaleProducts as $product)
                <a href="{{ route('product.show', $product) }}" 
                   class="product-card w-48 sm:w-56">
                    <img src="{{ asset('storage/'.$product->image) }}" 
                         class="w-full aspect-square object-cover">
                    <div class="p-3">
                        <h3 class="text-sm line-clamp-2">{{ $product->name }}</h3>
                        <p class="price-tag">Rp{{ number_format($product->price) }}</p>
                    </div>
                </a>
            @endforeach
        </div>

        <button data-carousel-next class="scroll-arrow scroll-arrow-right">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>
</section>
```

### **2. Category Scroll:**

```html
<div data-carousel class="relative">
    <button data-carousel-prev class="scroll-arrow scroll-arrow-left">
        <!-- Arrow SVG -->
    </button>

    <div data-carousel-container class="scroll-container">
        @foreach($categories as $category)
            <a href="#" class="category-item min-w-[100px]">
                <div class="w-16 h-16 rounded-2xl bg-cyan-100 flex items-center justify-center">
                    <span class="text-3xl">{{ $category->icon }}</span>
                </div>
                <span class="text-xs">{{ $category->name }}</span>
            </a>
        @endforeach
    </div>

    <button data-carousel-next class="scroll-arrow scroll-arrow-right">
        <!-- Arrow SVG -->
    </button>
</div>
```

### **3. Related Products:**

```html
<div data-carousel class="relative">
    <button data-carousel-prev class="scroll-arrow scroll-arrow-left">
        <!-- Arrow SVG -->
    </button>

    <div data-carousel-container class="scroll-container">
        @foreach($relatedProducts as $related)
            <div class="product-card w-44">
                <!-- Product content -->
            </div>
        @endforeach
    </div>

    <button data-carousel-next class="scroll-arrow scroll-arrow-right">
        <!-- Arrow SVG -->
    </button>
</div>
```

---

## ⚙️ Configuration

### **Scroll Speed:**

Edit `carousel.js`:

```javascript
// Faster scroll
const smoothScroll = (element, target, duration = 400) => {
    // ...
};

// Slower scroll
const smoothScroll = (element, target, duration = 800) => {
    // ...
};
```

### **Scroll Distance:**

```javascript
// Scroll 2 items (default)
const targetScroll = container.scrollLeft + scrollAmount * 2;

// Scroll 1 item
const targetScroll = container.scrollLeft + scrollAmount * 1;

// Scroll 3 items
const targetScroll = container.scrollLeft + scrollAmount * 3;
```

### **Easing Function:**

```javascript
// Bounce effect
const easeInOutCubic = t => t < 0.5 
    ? 4 * t * t * t 
    : (t - 1) * (2 * t - 2) * (2 * t - 2) + 1;

// Smooth ease
const easeInOutQuad = t => t < 0.5 
    ? 2 * t * t 
    : -1 + (4 - 2 * t) * t;

// Elastic
const easeOutElastic = t => {
    const c4 = (2 * Math.PI) / 3;
    return t === 0 ? 0 : t === 1 ? 1 
        : Math.pow(2, -10 * t) * Math.sin((t * 10 - 0.75) * c4) + 1;
};
```

---

## 🖱️ Mouse Drag

Carousel mendukung **drag to scroll**:

1. **Click & Hold** pada area scroll
2. **Drag** ke kiri atau kanan
3. **Release** untuk berhenti

**Features:**
- ✅ Cursor berubah menjadi "grabbing"
- ✅ Momentum scrolling (2x speed)
- ✅ Disable pada link & button
- ✅ Smooth deceleration

---

## 📱 Touch Support

### **Swipe Gestures:**

- **Swipe Left** → Scroll right
- **Swipe Right** → Scroll left
- **Fast Swipe** → Momentum scroll
- **Slow Swipe** → Controlled scroll

### **Mobile Optimization:**

```css
@media (max-width: 768px) {
    .scroll-container {
        gap: 0.75rem; /* Smaller gap */
        padding-bottom: 0.75rem;
    }
    
    .product-card {
        width: 11rem; /* 176px */
    }
    
    .scroll-arrow {
        width: 2.5rem;
        height: 2.5rem;
    }
}
```

---

## ⌨️ Keyboard Navigation

Carousel akan support keyboard di future update:

```javascript
// Arrow Left = Scroll previous
// Arrow Right = Scroll next
// Home = Scroll to start
// End = Scroll to end
```

---

## 🎭 Animation Details

### **Smooth Scroll Easing:**

```
Duration: 600ms
Easing: Cubic (4 * t³ for acceleration)
FPS: 60fps via requestAnimationFrame
```

### **Button Fade:**

```css
transition: opacity 0.3s ease-out;
```

### **Arrow Hover:**

```css
transform: scale(1.1);
transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
```

---

## 🔧 Troubleshooting

### **Arrows tidak muncul:**

**Solution:**
```html
<!-- Pastikan wrapper memiliki position: relative -->
<div data-carousel class="relative">
    <!-- ... -->
</div>
```

### **Scroll tidak smooth:**

**Solution:**
```css
/* Tambahkan ke container */
.scroll-container {
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
}
```

### **Item tidak snap:**

**Solution:**
```css
.scroll-container {
    scroll-snap-type: x mandatory;
}

.scroll-container > * {
    scroll-snap-align: center; /* atau start */
}
```

### **Drag tidak bekerja:**

**Solution:**
```javascript
// Pastikan carousel.js sudah di-import di app.js
import { initCarousel } from './carousel';
initCarousel();
```

---

## 🚀 Performance Tips

### **1. Lazy Load Images:**

```html
<img src="placeholder.jpg" 
     data-src="actual-image.jpg" 
     loading="lazy"
     class="lazy">
```

### **2. Reduce Items:**

```javascript
// Show max 20 items in carousel
const maxItems = 20;
const limitedProducts = products.slice(0, maxItems);
```

### **3. Use will-change:**

```css
.scroll-container {
    will-change: scroll-position;
}

.product-card {
    will-change: transform;
}
```

### **4. Optimize Images:**

- Use WebP format
- Size: 400x400px max
- Quality: 80%
- Compress with TinyPNG

---

## 📊 Browser Support

| Feature | Chrome | Firefox | Safari | Edge |
|---------|--------|---------|--------|------|
| Smooth Scroll | ✅ 61+ | ✅ 36+ | ✅ 15.4+ | ✅ 79+ |
| Scroll Snap | ✅ 69+ | ✅ 68+ | ✅ 11+ | ✅ 79+ |
| Touch Events | ✅ All | ✅ All | ✅ All | ✅ All |
| requestAnimationFrame | ✅ All | ✅ All | ✅ All | ✅ All |

---

## 💡 Best Practices

### **DO's:**

✅ Use consistent item widths
✅ Add gap between items
✅ Use snap-align for better UX
✅ Add touch-action for mobile
✅ Preload visible images
✅ Limit items to 20-30 per carousel

### **DON'Ts:**

❌ Don't use too many carousels per page
❌ Don't make items too small on mobile
❌ Don't forget keyboard accessibility
❌ Don't use autoplay (annoying)
❌ Don't hide overflow on parent

---

## 🎓 Advanced Examples

### **Multi-Row Carousel:**

```html
<div data-carousel-container class="scroll-container">
    <div class="grid grid-rows-2 gap-4">
        <div class="product-card">Item 1</div>
        <div class="product-card">Item 2</div>
    </div>
    <div class="grid grid-rows-2 gap-4">
        <div class="product-card">Item 3</div>
        <div class="product-card">Item 4</div>
    </div>
</div>
```

### **Vertical Carousel:**

```css
.scroll-container-vertical {
    flex-direction: column;
    overflow-y: auto;
    overflow-x: hidden;
    scroll-snap-type: y mandatory;
}
```

---

<div align="center">

**🎉 Enjoy Smooth Scrolling! 🎉**

Ultra smooth animations dengan horizontal scroll yang profesional!

[🏠 Back to README](README.md) | [🎬 Animation Guide](ANIMASI_GUIDE.md)

</div>
