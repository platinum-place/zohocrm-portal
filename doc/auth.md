# Documentación API GNB CRM Portal - OAuth Authentication

## Introducción

Este documento describe el proceso de autenticación OAuth2 para acceder a la API del GNB CRM Portal. La autenticación es
obligatoria para todas las solicitudes a la API y se realiza mediante tokens de acceso.

## Requisitos previos

- [Postman](https://www.postman.com/downloads/) (o cualquier cliente HTTP similar)
- Credenciales de acceso (client_id y client_secret)
- Colección de Postman "GNB CRM Portal" configurada

## Proceso de autenticación

La API utiliza el protocolo OAuth2 para la autenticación. Los clientes deben obtener un token de acceso antes de poder
realizar solicitudes a los endpoints protegidos.

### Endpoint de autenticación

``` 
POST /oauth/token
```

### Métodos de autenticación soportados

#### 1. Autenticación por credenciales de cliente (Client Credentials)

Este método se utiliza para autenticación de aplicaciones sin intervención del usuario.
**Parámetros requeridos:**

| Parámetro       | Descripción                      |
|-----------------|----------------------------------|
| `grant_type`    | Debe ser `client_credentials`    |
| `client_id`     | ID de cliente proporcionado      |
| `client_secret` | Secreto de cliente proporcionado |

**Ejemplo de solicitud:**

``` http
POST /oauth/token HTTP/1.1
Host: api.gnbcrm.com
Content-Type: application/x-www-form-urlencoded

grant_type=client_credentials&client_id=YOUR_CLIENT_ID&client_secret=YOUR_CLIENT_SECRET
```

#### 2. Autenticación por contraseña (Password Grant)

Este método se utiliza cuando una aplicación necesita autenticarse en nombre de un usuario.
**Parámetros requeridos:**

| Parámetro       | Descripción                      |
|-----------------|----------------------------------|
| `grant_type`    | Debe ser `password`              |
| `client_id`     | ID de cliente proporcionado      |
| `client_secret` | Secreto de cliente proporcionado |
| `username`      | Nombre de usuario                |
| `password`      | Contraseña del usuario           |

**Ejemplo de solicitud:**

``` http
POST /oauth/token HTTP/1.1
Host: api.gnbcrm.com
Content-Type: application/x-www-form-urlencoded

grant_type=password&client_id=YOUR_CLIENT_ID&client_secret=YOUR_CLIENT_SECRET&username=YOUR_USERNAME&password=YOUR_PASSWORD
```

### Respuesta exitosa

Una respuesta exitosa tendrá un código de estado HTTP 200 y devolverá un objeto JSON con el token de acceso:

``` json
{
  "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "expires_in": 3600,
  "token_type": "Bearer",
  "scope": null
}
```

### Errores de autenticación

Si la autenticación falla, la API devolverá un código de estado HTTP distinto a 200 y un objeto JSON con información
sobre el error:
{
"error_type": "invalid_client",
"description": "The client credentials are invalid"
}
| `invalid_request` | La solicitud carece de un parámetro requerido |
| --- | --- |
| `invalid_client` | La autenticación del cliente falló |
| `invalid_grant` | Las credenciales proporcionadas son incorrectas |
| `unauthorized_client` | El cliente no está autorizado para este método |
| `unsupported_grant_type` | El tipo de concesión no es soportado |

## Uso del token de acceso

Una vez obtenido el token de acceso, debe incluirse en todas las solicitudes a la API utilizando el encabezado de
autorización:

``` http
GET /api/resource HTTP/1.1
Host: api.gnbcrm.com
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
```

## Ejemplo con Postman

1. Abre la colección "GNB CRM Portal" en Postman
2. Localiza la solicitud para obtener token
3. Configura el Body como `x-www-form-urlencoded`
4. Agrega los parámetros necesarios según el método de autenticación elegido
5. Envía la solicitud
6. Copia el `access_token` recibido
7. Utiliza este token en el encabezado `Authorization` de las siguientes solicitudes

## Consideraciones importantes

- Los tokens tienen un tiempo de expiración definido por el campo `expires_in` (en segundos)
- Debes renovar el token antes de que expire para mantener el acceso a la API
- Almacena las credenciales de forma segura y nunca las expongas en código público o repositorios
- Utiliza HTTPS para todas las comunicaciones con la API
