# JavaScript Modules - JETXCEL S.A.S

Esta carpeta contiene todos los scripts JavaScript del proyecto organizados de manera modular para facilitar el mantenimiento y la reutilización.

## Estructura de Archivos

### Core Modules (Módulos Base)

#### `core.js`
- **Propósito**: Funcionalidad básica común a todas las páginas
- **Incluye**: 
  - Gestión del sidebar (colapsar/expandir)
  - Resaltado de navegación activa
  - Inicialización de tooltips
  - Funciones de navegación móvil

#### `utils.js`
- **Propósito**: Funciones utilitarias reutilizables
- **Incluye**:
  - Formateo de moneda
  - Validaciones comunes
  - Gestión de mensajes
  - Helpers de localStorage
  - Funciones de debounce y loading states

#### `select2-config.js`
- **Propósito**: Configuración centralizada de Select2
- **Incluye**:
  - Configuración base para todos los selects
  - Inicialización automática por tipo de elemento
  - Temas y estilos consistentes

#### `modals.js`
- **Propósito**: Gestión de modales y ventanas emergentes
- **Incluye**:
  - Comportamientos comunes de modales
  - Modales de confirmación dinámicos
  - Limpieza automática de formularios
  - Auto-focus en campos

### Page-Specific Modules (Módulos Específicos)

#### `ventas.js`
- **Propósito**: Funcionalidad específica del módulo de ventas
- **Incluye**:
  - Gestión del carrito de productos
  - Cálculos de totales y descuentos
  - Validación de ventas
  - Gestión de clientes y métodos de pago

#### `compras.js`
- **Propósito**: Funcionalidad específica del módulo de compras
- **Incluye**:
  - Formularios de compra
  - Gestión de proveedores
  - Cálculos de IVA y totales
  - Validación de productos y proveedores

## Cómo Usar

### Carga Automática
Los scripts se cargan automáticamente según la página actual:

```php
<?php
$currentPage = basename($_SERVER['PHP_SELF']);
switch($currentPage) {
    case 'ventas.php':
        echo '<script src="../../public/assets/js/ventas.js"></script>';
        break;
    case 'compras.php':
        echo '<script src="../../public/assets/js/compras.js"></script>';
        break;
}
?>
```

### Orden de Carga
1. **Librerías externas** (jQuery, Bootstrap, Select2)
2. **utils.js** - Funciones utilitarias
3. **core.js** - Funcionalidad base
4. **select2-config.js** - Configuración de Select2
5. **modals.js** - Gestión de modales
6. **Módulos específicos** - Según la página actual

### Dependencias
- **jQuery 3.6.0+**
- **Bootstrap 5.3.0+**
- **Select2 4.1.0+**

## Funciones Globales Disponibles

### JetxcelUtils
Objeto global con funciones utilitarias:

```javascript
// Formateo de moneda
JetxcelUtils.formatCurrency(850.50); // "$850.50"

// Mostrar mensajes
JetxcelUtils.showMessage('success', 'Operación exitosa');

// Validaciones
JetxcelUtils.validateRequired([
    {selector: '#campo', message: 'Campo requerido'}
]);

// LocalStorage
JetxcelUtils.storage.set('key', value);
JetxcelUtils.storage.get('key', defaultValue);
```

### Modales Dinámicos
```javascript
// Modal de confirmación
showConfirmModal('Título', 'Mensaje', function() {
    // Acción a confirmar
});

// Modal informativo
showInfoModal('Título', '<p>Contenido HTML</p>');
```

## Convenciones de Código

### Nomenclatura
- **Funciones**: camelCase (`calculateTotal`)
- **Variables**: camelCase (`productPrice`)
- **Constantes**: UPPER_CASE (`MAX_PRODUCTS`)
- **Selectores**: Usar IDs específicos (`#clientSelect`)

### Estructura de Módulos
```javascript
/**
 * Module Name - Description
 * Brief explanation of what this module does
 */

$(document).ready(function() {
    // Event handlers
    
    // Helper functions
    function helperFunction() {
        // Implementation
    }
});
```

### Manejo de Errores
```javascript
try {
    // Operación que puede fallar
} catch (error) {
    console.error('Error description:', error);
    JetxcelUtils.showMessage('error', 'Mensaje para el usuario');
}
```

## Agregar Nuevos Módulos

### 1. Crear el archivo
```bash
touch /public/assets/js/nuevo-modulo.js
```

### 2. Seguir la estructura estándar
```javascript
/**
 * Nuevo Módulo - Descripción
 */

$(document).ready(function() {
    // Tu código aquí
});
```

### 3. Agregar al footer.php
```php
case 'nueva-pagina.php':
    echo '<script src="../../public/assets/js/nuevo-modulo.js"></script>';
    break;
```

## Beneficios de esta Estructura

### ✅ Reutilización
- Funciones comunes disponibles en todos los módulos
- Configuraciones centralizadas
- Menos código duplicado

### ✅ Mantenimiento
- Cada módulo tiene una responsabilidad específica
- Fácil localización de problemas
- Actualizaciones independientes

### ✅ Performance
- Carga solo los scripts necesarios por página
- Menor tiempo de carga inicial
- Mejor experiencia de usuario

### ✅ Escalabilidad
- Fácil agregar nuevas funcionalidades
- Estructura preparada para crecimiento
- Separación clara de responsabilidades

## Migración Completada

Se han migrado exitosamente los siguientes scripts:
- ✅ Funcionalidad del sidebar y navegación
- ✅ Configuración de Select2
- ✅ Gestión de modales
- ✅ Módulo completo de ventas
- ✅ Módulo completo de compras
- ✅ Funciones utilitarias comunes

La funcionalidad existente se mantiene intacta mientras se mejora la organización y reutilización del código.
