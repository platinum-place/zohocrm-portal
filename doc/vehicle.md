# Documentación de API - Módulo de Vehículos

Esta documentación describe los endpoints disponibles para el módulo de vehículos en la API GNB CRM Portal.

## Autenticación

Todos los endpoints requieren autenticación OAuth2. Incluya el token de acceso en el encabezado de cada solicitud:

``` 
Authorization: Bearer {access_token}
```

## Endpoints

### 1. Obtener Marcas de Vehículos

Retorna un listado completo de todas las marcas de vehículos disponibles.
**Endpoint:**

``` 
POST /api/vehiculos/Marca
```

**Parámetros:** Ninguno
**Ejemplo de respuesta:**

``` json
[
  {
    "MARCA123": "Audi"
  },
  {
    "MARCA456": "BMW"
  },
  {
    "MARCA789": "Chevrolet"
  }
]
```

**Notas:**

- Las marcas se devuelven ordenadas alfabéticamente por nombre.
- Cada objeto contiene el ID de la marca como clave y su nombre como valor.

### 2. Obtener Modelos por Marca

Retorna todos los modelos disponibles para una marca específica.
**Endpoint:**

``` 
POST /api/vehiculos/Modelos/{marca_id}
```

**Parámetros URL:**

- `marca_id` (Obligatorio): ID de la marca para la cual se desean obtener los modelos

**Ejemplo de solicitud:**

``` 
POST /api/vehiculos/Modelos/MARCA123
```

**Ejemplo de respuesta:**

``` json
[
  {
    "MARCA123": [
      {
        "MODELO001": "A3"
      }
    ]
  },
  {
    "MARCA123": [
      {
        "MODELO002": "A4"
      }
    ]
  },
  {
    "MARCA123": [
      {
        "MODELO003": "Q5"
      }
    ]
  }
]
```

**Códigos de respuesta:**

- 200: OK - Retorna la lista de modelos
- 400: Bad Request - No se proporcionó brand_id
- 401: Unauthorized - Token de autenticación inválido

**Notas:**

- Los modelos se devuelven ordenados alfabéticamente por nombre.
- Si no hay modelos para la marca, se devuelve un array vacío.

### 3. Obtener Tipos de Vehículos

Retorna una lista completa de los tipos de vehículos disponibles en el sistema.
**Endpoint:**

``` 
POST /api/vehiculos/TipoVehiculo
```

**Parámetros:** Ninguno
**Ejemplo de respuesta:**

``` json
{
  "1": "Automóvil",
  "2": "Jeepeta",
  "3": "Camioneta",
  "4": "Furgoneta",
  "5": "Minivan",
  "6": "Camión",
  "7": "Veh. Pesado",
  "8": "Autobús",
  "9": "Minibus"
}
```
