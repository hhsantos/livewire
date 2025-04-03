# Sistema de Reservas de Vuelo

<p align="center">
<a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a>
</p>

## Acerca del Proyecto

Sistema de reservas de vuelo desarrollado con Laravel y Livewire, que permite a los usuarios gestionar reservas a través de una interfaz de calendario intuitiva y reactiva.

### Características Principales

- Calendario mensual interactivo
- Sistema CRUD completo para reservas
- Autenticación de usuarios
- Interfaz reactiva con Livewire
- Diseño responsive con Tailwind CSS

## Requisitos

- PHP >= 8.1
- Composer
- Node.js y NPM
- MySQL >= 5.7

## Instalación

1. Clonar el repositorio:
```bash
git clone [url-del-repositorio]
cd [nombre-del-proyecto]
```

2. Instalar dependencias PHP:
```bash
composer install
```

3. Instalar dependencias JavaScript:
```bash
npm install
```

4. Configurar el entorno:
```bash
cp .env.example .env
php artisan key:generate
```

5. Configurar la base de datos en el archivo .env:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_base_datos
DB_USERNAME=usuario
DB_PASSWORD=contraseña
```

6. Ejecutar las migraciones:
```bash
php artisan migrate
```

7. Compilar assets:
```bash
npm run dev
```

8. Iniciar el servidor:
```bash
php artisan serve
```

## Estructura del Proyecto

### Componentes Principales

- `app/Http/Livewire/Calendar.php`: Componente principal del calendario
- `app/Models/Reservation.php`: Modelo de reservas
- `resources/views/livewire/calendar.blade.php`: Vista del calendario

### Base de Datos

#### Tabla Reservations
- id (bigint)
- user_id (bigint)
- reservation_date (date)
- start_time (time)
- end_time (time)
- notes (text, nullable)
- timestamps

## Uso

### Gestión de Reservas

1. **Crear Reserva**:
   - Click en el número del día deseado
   - Completar el formulario modal
   - Guardar

2. **Editar Reserva**:
   - Click en una reserva existente (solo propias)
   - Modificar datos en el modal
   - Guardar cambios

3. **Eliminar Reserva**:
   - Click en una reserva propia
   - Click en "Eliminar"
   - Confirmar eliminación

### Navegación

- Usar flechas para cambiar de mes
- Filtrar reservas por período o texto
- Scroll vertical en días con múltiples reservas

## Características Técnicas

### Frontend

- **Tailwind CSS**: Framework de utilidades CSS
- **Alpine.js**: Framework JavaScript minimalista
- **Livewire**: Framework full-stack para Laravel

### Backend

- **Laravel**: Framework PHP
- **Eloquent ORM**: Para gestión de base de datos
- **Blade**: Motor de plantillas

## Seguridad

- Autenticación de usuarios integrada
- Validación de propiedad de reservas
- Protección CSRF
- Sanitización de inputs

## Próximas Mejoras

- [ ] Vista semanal/diaria
- [ ] Drag & drop para reservas
- [ ] Filtros adicionales
- [ ] Sistema de notificaciones
- [ ] Optimización de rendimiento

## Contribuir

1. Fork el proyecto
2. Crear rama de características (`git checkout -b feature/AmazingFeature`)
3. Commit cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir un Pull Request

## Licencia

Distribuido bajo la Licencia MIT. Ver `LICENSE` para más información.

## Contacto

[Tu Nombre] - [tu@email.com]

Enlace del proyecto: [https://github.com/username/repo]
