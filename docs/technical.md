# Documentación Técnica - Sistema de Reservas de Vuelo

## Arquitectura

### Componentes Livewire

#### Calendar.php
```php
class Calendar extends Component
{
    // Propiedades públicas
    public $currentDate;           // Fecha actual del calendario
    public $searchQuery;          // Búsqueda por texto
    public $dateFilter;           // Filtro de fecha (all, today, week, month)
    public $reservations;         // Colección de reservas
    
    // Propiedades del modal
    public $showReservationModal; // Control de visibilidad del modal
    public $selectedDate;         // Fecha seleccionada
    public $reservationId;        // ID de reserva en edición
    public $startTime;            // Hora de inicio
    public $endTime;             // Hora de fin
    public $notes;               // Notas de la reserva
}
```

### Estructura de Datos

#### Modelo Reservation
```php
class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'reservation_date',
        'start_time',
        'end_time',
        'notes'
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime'
    ];
}
```

## Implementación del Calendario

### Grid Layout
```html
<div class="grid grid-cols-7 gap-px bg-gray-200">
    <!-- Encabezados -->
    <!-- Grid de días -->
</div>
```

### Celda del Día
```html
<div class="h-[120px] relative">
    <!-- Número del día -->
    <div class="p-1 border-b">
        <span class="text-sm">{{ $day->format('j') }}</span>
    </div>
    
    <!-- Área de reservas -->
    <div class="overflow-y-auto absolute inset-x-0 bottom-0 top-8 p-1">
        <!-- Tarjetas de reservas -->
    </div>
</div>
```

### Tarjeta de Reserva
```html
<div class="mb-1 p-1 text-xs rounded">
    <div class="font-medium truncate">{{ $reservation->user->name }}</div>
    <div class="text-xs opacity-75 truncate">
        {{ Carbon\Carbon::parse($reservation->start_time)->format('H:i') }}
    </div>
    <div class="text-xs text-gray-500 truncate" title="{{ $reservation->notes }}">
        {{ $reservation->notes }}
    </div>
</div>
```

## Flujos de Datos

### Creación de Reserva
1. Click en día → `openReservationModal(date)`
2. Completar formulario
3. Submit → `saveReservation()`
4. Validación
5. Guardado en BD
6. Actualización del calendario

### Edición de Reserva
1. Click en reserva → `openReservationModal(date, id)`
2. Carga de datos existentes
3. Modificación
4. Submit → `saveReservation()`
5. Actualización en BD
6. Refresh del calendario

### Eliminación de Reserva
1. Click en "Eliminar"
2. Confirmación
3. `deleteReservation()`
4. Eliminación en BD
5. Actualización del calendario

## Optimizaciones

### Rendimiento
- Eager loading de relaciones
- Agrupación de reservas por fecha
- Truncamiento de texto largo
- Scroll virtual para días con muchas reservas

### UX/UI
- Feedback visual inmediato
- Tooltips para texto truncado
- Diferenciación visual de reservas propias
- Desactivación de interacción en días fuera del mes

## Seguridad

### Validaciones
```php
$this->validate([
    'startTime' => 'required|date_format:H:i',
    'endTime' => 'required|date_format:H:i|after:startTime',
    'notes' => 'nullable|string|max:255'
]);
```

### Autorizaciones
- Verificación de propiedad de reservas
- Middleware de autenticación
- Protección CSRF en formularios
- Sanitización de inputs

## Testing

### Casos de Prueba Recomendados
1. Creación de reservas
2. Edición de reservas propias
3. Intento de edición de reservas ajenas
4. Eliminación de reservas
5. Navegación entre meses
6. Filtrado de reservas
7. Validación de formularios

## Mantenimiento

### Tareas Periódicas
- Limpieza de reservas antiguas
- Optimización de índices de BD
- Monitoreo de rendimiento
- Backup de datos

### Logs
- Errores de validación
- Intentos de acceso no autorizado
- Operaciones CRUD
- Rendimiento del sistema
