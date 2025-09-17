# JETXCEL CSS Style Guide

## Table of Contents
1. [Code Organization](#code-organization)
2. [Naming Conventions](#naming-conventions)
3. [CSS Variables Usage](#css-variables-usage)
4. [Component Development](#component-development)
5. [Responsive Design](#responsive-design)
6. [Performance Best Practices](#performance-best-practices)
7. [Code Examples](#code-examples)

## Code Organization

### File Structure Rules
```
public/assets/css/
├── core.css          # Variables, base styles, utilities
├── layout.css        # Layout, navigation, structure
├── components.css    # Reusable UI components
└── [page].css        # Page-specific styles
```

### Import Order
1. **Core styles** (variables, base, utilities)
2. **Layout styles** (structure, navigation)
3. **Component styles** (reusable components)
4. **Page-specific styles** (conditional loading)

### CSS Organization Within Files
```css
/* File Header Comment */
/* 
 * JETXCEL - [File Purpose]
 * Description of what this file contains
 */

/* Section Comments */
/* ===== SECTION NAME ===== */

/* Component Comments */
/* Component Name */
.component-name {
    /* Properties grouped logically */
}
```

## Naming Conventions

### Class Naming
- Use **kebab-case** for all class names
- Use **semantic names** that describe purpose, not appearance
- Prefix page-specific classes when needed

```css
/* ✅ Good */
.product-card { }
.sale-container { }
.service-status-badge { }

/* ❌ Avoid */
.redButton { }
.big-text { }
.div1 { }
```

### BEM Methodology (When Appropriate)
```css
/* Block */
.card { }

/* Element */
.card__header { }
.card__body { }
.card__footer { }

/* Modifier */
.card--featured { }
.card--large { }
```

### CSS Variable Naming
```css
/* Color Variables */
--primary-color
--secondary-color
--success-color
--danger-color

/* Layout Variables */
--sidebar-width
--header-height
--border-radius

/* Component Variables */
--card-padding
--button-height
--modal-width
```

## CSS Variables Usage

### Color System
Always use CSS variables for colors to maintain consistency:

```css
/* ✅ Correct */
.button {
    background-color: var(--primary-color);
    color: var(--card-bg);
}

/* ❌ Avoid */
.button {
    background-color: #2563eb;
    color: white;
}
```

### Layout Consistency
Use variables for consistent spacing and sizing:

```css
/* ✅ Correct */
.card {
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    padding: var(--container-padding);
}
```

### Fallback Values
Provide fallbacks for older browsers:

```css
.component {
    background-color: #2563eb; /* fallback */
    background-color: var(--primary-color);
}
```

## Component Development

### Component Structure
```css
/* Base Component */
.component-name {
    /* Layout properties */
    display: flex;
    position: relative;
    
    /* Box model */
    width: 100%;
    padding: 1rem;
    margin-bottom: 1rem;
    
    /* Visual properties */
    background-color: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    
    /* Typography */
    font-size: 1rem;
    color: var(--text-color);
    
    /* Transitions */
    transition: transform 0.2s, box-shadow 0.2s;
}

/* States */
.component-name:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.component-name:focus {
    outline: 2px solid var(--primary-color);
    outline-offset: 2px;
}

/* Variants */
.component-name--large {
    padding: 2rem;
    font-size: 1.2rem;
}

/* Elements */
.component-name__header {
    margin-bottom: 1rem;
    font-weight: 600;
}

.component-name__body {
    flex: 1;
}
```

### Component Checklist
- [ ] Uses CSS variables for colors and spacing
- [ ] Includes hover and focus states
- [ ] Has smooth transitions
- [ ] Is responsive (mobile-first)
- [ ] Follows accessibility guidelines
- [ ] Has consistent naming

## Responsive Design

### Mobile-First Approach
Always start with mobile styles, then enhance for larger screens:

```css
/* Mobile styles (default) */
.component {
    display: block;
    width: 100%;
}

/* Tablet and up */
@media (min-width: 768px) {
    .component {
        display: flex;
        width: 50%;
    }
}

/* Desktop and up */
@media (min-width: 1024px) {
    .component {
        width: 33.333%;
    }
}
```

### Breakpoint Variables
```css
:root {
    --breakpoint-sm: 576px;
    --breakpoint-md: 768px;
    --breakpoint-lg: 992px;
    --breakpoint-xl: 1200px;
}
```

### Grid Systems
Use CSS Grid for complex layouts:

```css
.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
}

/* Responsive adjustment */
@media (max-width: 768px) {
    .product-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    }
}
```

## Performance Best Practices

### Efficient Selectors
```css
/* ✅ Efficient */
.card { }
.card-header { }

/* ❌ Inefficient */
div.container > ul li a.link { }
```

### Minimize Repaints
```css
/* ✅ Use transform for animations */
.card:hover {
    transform: translateY(-2px);
}

/* ❌ Avoid changing layout properties */
.card:hover {
    margin-top: -2px;
}
```

### Optimize Images and Assets
```css
/* Use appropriate units */
.component {
    width: 100%;        /* Flexible */
    max-width: 400px;   /* Constrained */
    height: auto;       /* Maintain ratio */
}
```

## Code Examples

### Product Card Component
```css
/* Product Card */
.product-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: var(--border-radius);
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
    cursor: pointer;
}

.product-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.1);
    border-color: var(--primary-color);
}

.product-card__image {
    height: 180px;
    overflow: hidden;
    position: relative;
    background: var(--light-bg);
}

.product-card__image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.product-card:hover .product-card__image img {
    transform: scale(1.05);
}

.product-card__content {
    padding: 1rem;
}

.product-card__title {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    line-height: 1.3;
    
    /* Text truncation */
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-card__price {
    font-weight: 700;
    color: var(--primary-color);
    font-size: 1.2rem;
}
```

### Form Component
```css
/* Form Section */
.form-section {
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--card-border);
}

.form-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.form-section__title {
    font-weight: 600;
    font-size: 1.1rem;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group__label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: var(--text-secondary);
}

.form-group__input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid var(--card-border);
    border-radius: var(--border-radius);
    font-size: 1rem;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.form-group__input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}
```

### Status Badge Component
```css
/* Status Badge */
.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* Status Variants */
.status-badge--pending {
    background-color: var(--tag-orange);
    color: white;
}

.status-badge--in-progress {
    background-color: var(--tag-blue);
    color: white;
}

.status-badge--completed {
    background-color: var(--tag-green);
    color: white;
}

.status-badge--delayed {
    background-color: var(--tag-red);
    color: white;
}
```

## Accessibility Guidelines

### Focus States
Always provide visible focus indicators:

```css
.button:focus {
    outline: 2px solid var(--primary-color);
    outline-offset: 2px;
}
```

### Color Contrast
Ensure sufficient contrast ratios:

```css
/* Check contrast ratios */
.text-primary { color: #1e293b; } /* 4.5:1 minimum */
.text-secondary { color: #64748b; } /* 3:1 minimum for large text */
```

### Screen Reader Support
Use semantic HTML and appropriate ARIA labels:

```css
/* Hide decorative elements from screen readers */
.decorative-icon {
    aria-hidden: true;
}

/* Visually hidden but accessible to screen readers */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}
```

## Testing Checklist

### Cross-Browser Testing
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)

### Responsive Testing
- [ ] Mobile (320px - 767px)
- [ ] Tablet (768px - 1023px)
- [ ] Desktop (1024px+)
- [ ] Large screens (1400px+)

### Performance Testing
- [ ] CSS file sizes optimized
- [ ] No unused CSS rules
- [ ] Efficient selectors used
- [ ] Animations use transform/opacity

### Accessibility Testing
- [ ] Keyboard navigation works
- [ ] Focus indicators visible
- [ ] Color contrast meets WCAG standards
- [ ] Screen reader compatibility

---

**Version**: 1.0  
**Last Updated**: September 2025  
**Team**: JETXCEL Development
