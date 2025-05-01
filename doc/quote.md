# Documentación API de Cotizaciones

## Índice

1. [Introducción](#introducci%C3%B3n)
2. [Autenticación](#autenticaci%C3%B3n)
3. [Endpoints de Cotizaciones](#endpoints-de-cotizaciones)
    - [Cotización de Vehículos](#cotizaci%C3%B3n-de-veh%C3%ADculos)
    - [Emisión de Póliza de Vehículos](#emisi%C3%B3n-de-p%C3%B3liza-de-veh%C3%ADculos)
    - [Cotización de Vida](#cotizaci%C3%B3n-de-vida)
    - [Emisión de Póliza de Vida](#emisi%C3%B3n-de-p%C3%B3liza-de-vida)

4. [Códigos de Respuesta](#c%C3%B3digos-de-respuesta)
5. [Consideraciones](#consideraciones)

## Introducción

Esta API permite a los usuarios realizar cotizaciones de seguros y emitir pólizas en diferentes categorías como
vehículos y seguros de vida. La API sigue los principios REST y devuelve respuestas en formato JSON.

## Autenticación

Para acceder a los endpoints de esta API, es necesario incluir las credenciales de autenticación apropiadas. Contacte
con el equipo de soporte para obtener sus credenciales de acceso.

## Endpoints de Cotizaciones

### Cotización de Vehículos

Este endpoint permite obtener cotizaciones para seguros de vehículos.
**URL:** `/api/cotizador/colectiva`
**Método:** `POST`
**Parámetros de Solicitud:**

| Parámetro       | Tipo    | Requerido | Descripción                            |
|-----------------|---------|-----------|----------------------------------------|
| MontoAsegurado  | Decimal | Sí        | Valor asegurado del vehículo           |
| Anio            | Integer | Sí        | Año del vehículo                       |
| Actividad       | String  | Sí        | Uso del vehículo (personal, comercial) |
| Marca           | String  | Sí        | Marca del vehículo                     |
| Modelo          | String  | Sí        | Modelo del vehículo                    |
| TipoVehiculo    | String  | Sí        | Tipo de vehículo                       |
| NombreCliente   | String  | Sí        | Nombre completo del cliente            |
| FechaNacimiento | Date    | Sí        | Fecha de nacimiento (YYYY-MM-DD)       |
| IdCliente       | String  | Sí        | Número de identificación del cliente   |
| Email           | String  | Sí        | Correo electrónico del cliente         |
| TelefMovil      | String  | Sí        | Teléfono móvil del cliente             |
| TelefResidencia | String  | No        | Teléfono de residencia del cliente     |
| TelefTrabajo    | String  | No        | Teléfono del trabajo del cliente       |
| Chasis          | String  | Sí        | Número de chasis del vehículo          |
| Placa           | String  | Sí        | Número de placa del vehículo           |

**Ejemplo de Solicitud:**

``` json
{
  "MontoAsegurado": 500000,
  "Anio": 2020,
  "Actividad": "Personal",
  "Marca": "Toyota",
  "Modelo": "Corolla",
  "TipoVehiculo": "Sedan",
  "NombreCliente": "Juan Pérez",
  "FechaNacimiento": "1985-05-15",
  "IdCliente": "001-1234567-8",
  "Email": "juan.perez@ejemplo.com",
  "TelefMovil": "809-555-1234",
  "TelefResidencia": "809-555-5678",
  "TelefTrabajo": "809-555-8765",
  "Chasis": "1HGBH41JXMN109186",
  "Placa": "A123456"
}
```

**Respuesta Exitosa:**

``` json
[
  {
    "Passcode": "",
    "OfertaID": "",
    "Prima": 10500.25,
    "Impuesto": 1680.04,
    "PrimaTotal": 12180.29,
    "PrimaCuota": 1015.02,
    "Planid": "",
    "Plan": "Plan Anual Full",
    "Aseguradora": "Seguros Ejemplo",
    "Idcotizacion": "3222373000451832001",
    "Fecha": "2023-07-01",
    "CoberturasList": "",
    "Error": "",
    "Alerta": "Cotización sujeta a inspección"
  },
  {
    "Passcode": "",
    "OfertaID": "",
    "Prima": 9800.50,
    "Impuesto": 1568.08,
    "PrimaTotal": 11368.58,
    "PrimaCuota": 947.38,
    "Planid": "",
    "Plan": "Plan Anual Full",
    "Aseguradora": "Aseguradora XYZ",
    "Idcotizacion": "3222373000451832002",
    "Fecha": "2023-07-01",
    "CoberturasList": "",
    "Error": "",
    "Alerta": ""
  }
]
```

### Emisión de Póliza de Vehículos

Este endpoint permite emitir una póliza basada en una cotización previamente generada.
**URL:** `/api/cotizador/EmitirAuto`
**Método:** `POST`
**Parámetros de Solicitud:**

| Parámetro | Tipo   | Requerido | Descripción                     |
|-----------|--------|-----------|---------------------------------|
| cotzid    | String | Sí        | ID de la cotización a convertir |

**Ejemplo de Solicitud:**

``` json
{
  "cotzid": "3222373000451832001"
}
```

**Respuesta Exitosa:**

``` json
{
  "code": 200,
  "status": "success"
}
```

### Cotización de Vida

Este endpoint permite obtener cotizaciones para seguros de vida.
**URL:** `/api/cotizador/CotizaVida`
**Método:** `POST`
**Parámetros de Solicitud:**

| Parámetro       | Tipo    | Requerido | Descripción                          |
|-----------------|---------|-----------|--------------------------------------|
| EdadAsegurado   | Integer | Sí        | Edad del asegurado principal         |
| EdadCodeudor    | Integer | No        | Edad del codeudor (si aplica)        |
| MontoAsegurado  | Decimal | Sí        | Monto a asegurar                     |
| NombreCliente   | String  | Sí        | Nombre completo del cliente          |
| FechaNacimiento | Date    | Sí        | Fecha de nacimiento (YYYY-MM-DD)     |
| IdCliente       | String  | Sí        | Número de identificación del cliente |
| Email           | String  | Sí        | Correo electrónico del cliente       |
| TelefMovil      | String  | Sí        | Teléfono móvil del cliente           |
| TelefResidencia | String  | No        | Teléfono de residencia del cliente   |
| TelefTrabajo    | String  | No        | Teléfono del trabajo del cliente     |

**Ejemplo de Solicitud:**

``` json
{
  "EdadAsegurado": 35,
  "EdadCodeudor": 32,
  "MontoAsegurado": 1000000,
  "NombreCliente": "María Rodríguez",
  "FechaNacimiento": "1988-10-20",
  "IdCliente": "001-9876543-2",
  "Email": "maria.rodriguez@ejemplo.com",
  "TelefMovil": "809-555-4321",
  "TelefResidencia": "809-555-8765",
  "TelefTrabajo": "809-555-1234"
}
```

**Respuesta Exitosa:**

``` json
[
  {
    "Passcode": "",
    "OfertaID": "",
    "Prima": 2100.50,
    "Impuesto": 336.08,
    "PrimaTotal": 2436.58,
    "PrimaCuota": 203.05,
    "Planid": "",
    "Plan": "Vida Protegido",
    "Aseguradora": "Seguros Ejemplo",
    "Idcotizacion": "3222373000453456001",
    "Fecha": "2023-07-01",
    "CoberturasList": "",
    "Error": "",
    "Alerta": ""
  }
]
```

### Emisión de Póliza de Vida

Este endpoint permite emitir una póliza de vida basada en una cotización previamente generada.
**URL:** `/api/cotizador/EmitirVida`
**Método:** `POST`
**Parámetros de Solicitud:**

| Parámetro     | Tipo   | Requerido | Descripción                     |
|---------------|--------|-----------|---------------------------------|
| Identificador | String | Sí        | ID de la cotización a convertir |

**Ejemplo de Solicitud:**

``` json
{
  "Identificador": "3222373000453456001"
}
```

**Respuesta Exitosa:**

``` json
{
  "code": 200,
  "status": "success"
}
```

## Códigos de Respuesta

| Código | Descripción                                                |
|--------|------------------------------------------------------------|
| 200    | La solicitud se procesó correctamente                      |
| 400    | Error en la solicitud (parámetros faltantes o incorrectos) |
| 401    | Error de autenticación                                     |
| 404    | Recurso no encontrado                                      |
| 500    | Error interno del servidor                                 |

## Estimación de Seguro de Desempleo

Este endpoint permite calcular la prima de un seguro de desempleo según los datos proporcionados del solicitante.
**URL:** `/api/cotizador/CotizaDesempleo`
**Método:** `POST`
**Parámetros de Solicitud:**

| Parámetro       | Tipo    | Requerido | Descripción                                  |
|-----------------|---------|-----------|----------------------------------------------|
| MontoOriginal   | Decimal | Sí        | Monto original del préstamo                  |
| Plazo           | Integer | Sí        | Plazo del préstamo (en años)                 |
| TiempoLaborando | Integer | Sí        | Tiempo laborando en trabajo actual (en años) |
| Cuota           | Decimal | Sí        | Cuota mensual del préstamo                   |
| Cliente         | String  | Sí        | Nombre completo del cliente                  |
| IdenCliente     | String  | Sí        | Número de identificación del cliente         |
| Direccion       | String  | Sí        | Dirección completa del cliente               |
| Telefono        | String  | Sí        | Número de teléfono del cliente               |

**Ejemplo de Solicitud:**

``` json
{
  "MontoOriginal": 500000,
  "Plazo": 5,
  "TiempoLaborando": 3,
  "Cuota": 12500,
  "Cliente": "Juan Pérez",
  "IdenCliente": "001-1234567-8",
  "Direccion": "Calle Principal #123, Santo Domingo",
  "Telefono": "809-555-1234"
}
```

**Ejemplo de Respuesta:**

``` json
[
  {
    "Impuesto": 6500.45,
    "PrimaPeriodo": "",
    "PrimaTotal": "",
    "identificador": "4567890123",
    "Cliente": "Juan Pérez",
    "Direccion": "Calle Principal #123, Santo Domingo",
    "Fecha": "2023-06-15",
    "TipoEmpleado": "",
    "IdenCliente": "001-1234567-8",
    "Aseguradora": "Seguros Confianza",
    "MontoOriginal": 500000,
    "Cuota": 12500,
    "PlazoMese": 60,
    "Total": 7475.52
  },
  {
    "Impuesto": 7200.80,
    "PrimaPeriodo": "",
    "PrimaTotal": "",
    "identificador": "4567890124",
    "Cliente": "Juan Pérez",
    "Direccion": "Calle Principal #123, Santo Domingo",
    "Fecha": "2023-06-15",
    "TipoEmpleado": "",
    "IdenCliente": "001-1234567-8",
    "Aseguradora": "Aseguradora Universal",
    "MontoOriginal": 500000,
    "Cuota": 12500,
    "PlazoMese": 60,
    "Total": 8280.92
  }
]
```

## Estimación de Seguro de Incendio

Este endpoint permite calcular la prima de un seguro de incendio y líneas aliadas para bienes inmuebles.
**URL:** `/api/cotizador/CotizaIncendio`
**Método:** `POST`
**Parámetros de Solicitud:**

| Parámetro        | Tipo    | Requerido | Descripción                                     |
|------------------|---------|-----------|-------------------------------------------------|
| ValorFinanciado  | Decimal | Sí        | Valor financiado o monto asegurado del inmueble |
| Cliente          | String  | Sí        | Nombre completo del cliente                     |
| IdenCliente      | String  | Sí        | Número de identificación del cliente            |
| Direccion        | String  | Sí        | Dirección completa del inmueble                 |
| Telefono         | String  | Sí        | Número de teléfono del cliente                  |
| TipoPropiedad    | String  | Sí        | Tipo de propiedad (casa, apartamento, local)    |
| TipoConstruccion | String  | Sí        | Material de construcción predominante           |

**Ejemplo de Solicitud:**

``` json
{
  "ValorFinanciado": 3500000,
  "Cliente": "María Rodríguez",
  "IdenCliente": "001-2345678-9",
  "Direccion": "Avenida Central #456, Apto. 302, Santiago",
  "Telefono": "809-555-5678",
  "TipoPropiedad": "Apartamento",
  "TipoConstruccion": "Hormigón"
}
```

**Ejemplo de Respuesta:**

``` json
[
  {
    "Impuesto": 5250.75,
    "PrimaPeriodo": "",
    "PrimaTotal": "",
    "identificador": "4567890125",
    "Cliente": "María Rodríguez",
    "Direccion": "Avenida Central #456, Apto. 302, Santiago",
    "Fecha": "2023-06-15",
    "TipoPropiedad": "Apartamento",
    "IdenCliente": "001-2345678-9",
    "Aseguradora": "Seguros Atlántica",
    "ValorFinanciado": 3500000,
    "TipoConstruccion": "Hormigón",
    "Total": 6038.36
  },
  {
    "Impuesto": 4950.20,
    "PrimaPeriodo": "",
    "PrimaTotal": "",
    "identificador": "4567890126",
    "Cliente": "María Rodríguez",
    "Direccion": "Avenida Central #456, Apto. 302, Santiago",
    "Fecha": "2023-06-15",
    "TipoPropiedad": "Apartamento",
    "IdenCliente": "001-2345678-9",
    "Aseguradora": "Seguros Nacionales",
    "ValorFinanciado": 3500000,
    "TipoConstruccion": "Hormigón",
    "Total": 5692.73
  }
]
```

## Emisión de Póliza de Desempleo

Este endpoint permite emitir una póliza de seguro de desempleo a partir de una cotización previamente generada.
**URL:** `/api/cotizador/EmitirDesempleo`
**Método:** `POST`
**Parámetros de Solicitud:**

| Parámetro     | Tipo   | Requerido | Descripción                  |
|---------------|--------|-----------|------------------------------|
| Identificador | String | Sí        | ID de la cotización a emitir |

**Ejemplo de Solicitud:**

``` json
{
  "Identificador": "4567890123"
}
```

**Ejemplo de Respuesta:**

``` json
{
  "code": 200,
  "status": "success"
}
```

## Emisión de Póliza de Incendio

Este endpoint permite emitir una póliza de seguro de incendio a partir de una cotización previamente generada.
**URL:** `/api/cotizador/EmitirIncendio`
**Método:** `POST`
**Parámetros de Solicitud:**

| Parámetro     | Tipo   | Requerido | Descripción                  |
|---------------|--------|-----------|------------------------------|
| Identificador | String | Sí        | ID de la cotización a emitir |

**Ejemplo de Solicitud:**

``` json
{
  "Identificador": "4567890125"
}
```

**Ejemplo de Respuesta:**

``` json
{
  "code": 200,
  "status": "success"
}
```

## Listado de productos

Este endpoint ver los productos disponible.
**URL:** `/api/Productos`
**Método:** `GET`

**Ejemplo de Respuesta:**

``` json
{
  1: Auto,
  2: "Vida"
}
```
