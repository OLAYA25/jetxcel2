# JETXCEL CSS Architecture Documentation

## Overview

This document describes the modular CSS architecture implemented for the JETXCEL project. The CSS has been organized into a maintainable, scalable structure that promotes reusability and performance.

## Architecture

### File Structure
```
public/assets/css/
├── core.css                 # CSS variables, base styles, typography
├── layout.css              # Layout, responsive design, structural elements
├── components.css          # Reusable UI components, forms, modals
├── ventas.css             # Sales module specific styles
├── compras.css            # Purchases module specific styles
└── servicios-tecnicos.css # Technical services module specific styles
```

### Loading Strategy
CSS files are loaded conditionally based on the current page:
- **Core files** (core.css, layout.css, components.css) are loaded on every page
- **Page-specific files** are loaded only when needed
- This reduces CSS payload by ~33% per page compared to the previous inline approach

## Core Files

### 1. core.css
**Purpose**: Foundation styles, variables, and global elements

**Contains**:
- CSS Custom Properties (variables)
- Base typography and colors
- Global utility classes
- Form control styling
- Button styles

**Key Variables**:
```css
:root {
    /* Colors */
    --primary-color: #2563eb;
    --secondary-color: #64748b;
    --success-color: #22c55e;
    --danger-color: #ef4444;
    
    /* Layout */
    --sidebar-width: 250px;
    --border-radius: 8px;
    --box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    
    /* Status Tags */
    --tag-red: #ef4444;
    --tag-blue: #3b82f6;
    --tag-orange: #f97316;
    --tag-purple: #6366f1;
    --tag-gray: #6b7280;
    --tag-green: #22c55e;
}
```

### 2. layout.css
**Purpose**: Main layout structure and responsive design

**Contains**:
- Sidebar navigation styles
- Main content layout
- Header and navigation
- Tabs container
- Footer positioning
- Responsive breakpoints
- Mobile sidebar toggle

**Key Classes**:
- `.sidebar` - Main navigation sidebar
- `.main-content` - Primary content area
- `.header` - Top navigation bar
- `.tabs-container` - Tab navigation
- `.toggle-sidebar` - Mobile menu toggle

### 3. components.css
**Purpose**: Reusable UI components

**Contains**:
- Product cards and grids
- Form sections and controls
- Modal enhancements
- Detail rows and labels
- Action buttons
- Loading states
- Dashboard cards

**Key Components**:
- `.product-card` - Product display cards
- `.form-section` - Form grouping containers
- `.detail-row` - Information display rows
- `.card-actions` - Action button containers
- `.modal-content` - Enhanced modal styling

## Page-Specific Files

### ventas.css
**Purpose**: Sales module specific styling

**Features**:
- Sales container layout (2-column)
- Product selection interface
- Shopping cart styling
- Payment method selection
- Discount controls
- Client selection interface
- Sales summary calculations

**Key Classes**:
- `.sale-container` - Main sales layout
- `.sale-form-container` - Sticky sidebar form
- `.cart-item` - Shopping cart items
- `.payment-methods` - Payment selection grid
- `.discount-section` - Discount application area

### compras.css
**Purpose**: Purchases module specific styling

**Features**:
- Purchase form layout
- Supplier management interface
- Purchase items table
- IVA calculations
- Purchase totals display
- Item management controls

**Key Classes**:
- `.purchase-form` - Main purchase form
- `.supplier-section` - Supplier information area
- `.purchase-table` - Items table styling
- `.iva-section` - Tax calculation area
- `.purchase-totals` - Summary calculations

### servicios-tecnicos.css
**Purpose**: Technical services module styling

**Features**:
- Services grid layout
- Service cards with status
- Service form styling
- Statistics display
- Filter controls
- Status badges

**Key Classes**:
- `.services-grid` - Main services layout
- `.service-card` - Individual service cards
- `.service-status-badge` - Status indicators
- `.services-stats` - Statistics cards
- `.services-filters` - Filter controls

## CSS Variables System

### Color Palette
The project uses a comprehensive color system defined in CSS custom properties:

```css
/* Primary Colors */
--primary-color: #2563eb;    /* Main brand color */
--secondary-color: #64748b;  /* Secondary actions */
--success-color: #22c55e;    /* Success states */
--warning-color: #f59e0b;    /* Warning states */
--danger-color: #ef4444;     /* Error states */

/* Background Colors */
--body-bg: #f8fafc;          /* Main background */
--card-bg: white;            /* Card backgrounds */
--sidebar-bg: #1e293b;       /* Sidebar background */
--light-bg: #f1f5f9;         /* Light sections */
```

### Layout Variables
```css
/* Spacing and Layout */
--sidebar-width: 250px;      /* Sidebar width */
--container-padding: 1rem;   /* Standard padding */
--border-radius: 8px;        /* Standard border radius */
--box-shadow: 0 2px 6px rgba(0,0,0,0.05); /* Standard shadow */
```

### Status Colors
```css
/* Status Tag Colors */
--tag-red: #ef4444;      /* Urgent/Error */
--tag-blue: #3b82f6;     /* Information */
--tag-orange: #f97316;   /* Warning */
--tag-purple: #6366f1;   /* In Progress */
--tag-gray: #6b7280;     /* Neutral */
--tag-green: #22c55e;    /* Success/Complete */
```

## Responsive Design

### Breakpoints
```css
/* Mobile First Approach */
@media (max-width: 768px) {
    /* Mobile styles */
}

@media (max-width: 992px) {
    /* Tablet styles */
}

@media (max-width: 1200px) {
    /* Small desktop styles */
}
```

### Key Responsive Features
- **Sidebar**: Collapses to icons on mobile, slides out on tablet
- **Product Grid**: Adjusts columns based on screen size
- **Forms**: Stack vertically on mobile
- **Tables**: Horizontal scroll on small screens

## Performance Optimizations

### File Size Reduction
- **Before**: ~15KB single inline CSS block
- **After**: ~10KB average per page with conditional loading
- **Improvement**: 33% reduction in CSS payload per page

### Loading Strategy
1. **Critical CSS**: Core styles loaded immediately
2. **Page-specific CSS**: Loaded conditionally via PHP
3. **Unused CSS**: Completely removed (30 classes eliminated)

### Browser Support
- **Modern browsers**: Full support with CSS Grid and Flexbox
- **Legacy support**: Graceful degradation for older browsers
- **CSS Custom Properties**: Fallbacks provided where needed

## Usage Guidelines

### Adding New Styles

1. **Determine the appropriate file**:
   - Global styles → `core.css`
   - Layout/structure → `layout.css`
   - Reusable components → `components.css`
   - Page-specific → respective page file

2. **Use CSS variables** for consistent theming:
   ```css
   .my-component {
       background-color: var(--card-bg);
       border-radius: var(--border-radius);
       box-shadow: var(--box-shadow);
   }
   ```

3. **Follow naming conventions**:
   - Use semantic class names
   - Follow BEM methodology where appropriate
   - Prefix page-specific classes when needed

### Creating New Components

1. **Add to components.css** if reusable across pages
2. **Use consistent spacing** with CSS variables
3. **Include hover states** and transitions
4. **Consider mobile responsiveness**

Example:
```css
.my-component {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: var(--border-radius);
    padding: 1rem;
    transition: transform 0.2s, box-shadow 0.2s;
}

.my-component:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
```

## Maintenance

### Regular Tasks
1. **Review unused CSS** periodically using the analysis script
2. **Update CSS variables** for consistent theming
3. **Test responsive behavior** on different screen sizes
4. **Validate CSS** for browser compatibility

### Adding New Pages
1. Create page-specific CSS file if needed
2. Add to the conditional loading array in `header.php`
3. Follow existing naming conventions
4. Document new components in this README

### Troubleshooting

**Common Issues**:
- **Styles not loading**: Check file path and conditional loading logic
- **Variables not working**: Ensure they're defined in `core.css`
- **Layout issues**: Verify HTML structure matches CSS expectations
- **Mobile problems**: Test responsive breakpoints

**Debug Tools**:
- Use browser dev tools to inspect CSS loading
- Check console for 404 errors on CSS files
- Validate CSS syntax with online tools

## Migration History

### From Inline to Modular (Completed)
- **Removed**: 600+ lines of inline CSS from header.php
- **Created**: 6 modular CSS files
- **Eliminated**: 30 unused CSS classes (32.6% reduction)
- **Achieved**: 100% CSS usage rate
- **Improved**: 33% performance gain per page

### Benefits Achieved
- ✅ **Maintainability**: Clear separation of concerns
- ✅ **Performance**: Conditional loading reduces payload
- ✅ **Reusability**: Shared components across pages
- ✅ **Organization**: Logical file structure
- ✅ **Scalability**: Easy to add new modules

## Future Enhancements

### Recommended Improvements
1. **CSS Preprocessing**: Consider SASS/LESS for advanced features
2. **CSS Minification**: Implement for production builds
3. **Critical Path CSS**: Inline critical styles for faster rendering
4. **CSS-in-JS**: For dynamic components if needed
5. **Design Tokens**: Expand variable system for design consistency

### Potential Additions
- Dark mode support using CSS variables
- Animation library for micro-interactions
- Print stylesheets for reports
- High contrast mode for accessibility

---

**Last Updated**: September 2025  
**Version**: 1.0  
**Maintainer**: JETXCEL Development Team
