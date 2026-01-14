# 🥋 Kunfumanager

**Kunfumanager** es una aplicación web diseñada para la **gestión integral de un dojo real**, permitiendo administrar alumnos, profesores, horarios, inventario y finanzas desde una única plataforma.

El proyecto surge a partir de una **necesidad real de digitalización** en la gestión de pequeños centros deportivos y está enfocado a un posible uso en **entornos de producción reales**.

---

## 📖 Descripción general

Kunfumanager se compone de:

- 🌐 **Página web pública estática**, accesible por cualquier persona, con información general del dojo.
- 🔐 **Panel privado de gestión**, accesible únicamente mediante inicio de sesión.

Los usuarios **no pueden crear cuentas libremente** desde la web pública. Las credenciales son gestionadas internamente por la administración del dojo.

El acceso y las funcionalidades disponibles dependen del **rol del usuario**.

---

## 👥 Roles de usuario y funcionalidades

### 🧑‍🎓 Alumno
- Acceso a su perfil personal
- Visualización de su ficha técnica
- Consulta de horarios de entrenamiento
- Acceso a la tienda / inventario
- Solicitud de equipamiento
- Pago de mensualidades o anualidades
- Generación de su ficha en PDF
- Consulta del tiempo restante de entrenamiento

---

### 🧑‍🏫 Profesor
Incluye todo lo anterior, además de:
- Visualización de su ficha de profesor
- Consulta de horarios con alumnos asignados
- Gestión de clases
- Visualización y edición parcial de fichas de alumnos
- Registro de ausencias del alumnado
- Solicitudes de material de entrenamiento
- Gestión de solicitudes propias

---

### 🛠️ Administrador
Acceso completo a la aplicación:
- Gestión de alumnos, profesores y roles
- Gestión de horarios y grupos
- Gestión de ausencias de profesores
- Control y aprobación de solicitudes de inventario
- Gestión completa del inventario
- Registro de gastos (materiales, suministros, servicios)
- Registro de ingresos (cuotas, compras)
- Generación y gestión de facturas
- Informes financieros mensuales y trimestrales

---

## 💰 Gestión financiera

El módulo financiero permite:

- Control de **mensualidades de alumnos**
- Registro de **ingresos y gastos**
- Generación de **facturas y recibos**
- Cálculo de beneficios o pérdidas
- Historial económico del dojo
- Preparación para **facturación electrónica**, cumpliendo la normativa vigente

---

## 📦 Inventario y tienda

Kunfumanager incluye un sistema de gestión de inventario que permite:

- Controlar materiales y equipamiento
- Solicitudes de equipamiento por alumnos y profesores
- Aprobación o rechazo por parte de administración
- Registro de compras y consumo de material

---

## 🧱 Tecnologías utilizadas

- **Backend:** PHP con Laravel  
- **Frontend:** JavaScript y React  
- **Base de datos:** MySQL  
- **Arquitectura:** Aplicación web con control de acceso por roles  

---

## 🔐 Seguridad y acceso

- Sistema de autenticación con inicio de sesión
- Control de permisos basado en roles
- Protección de datos personales conforme al RGPD
- Separación clara entre web pública y panel privado

---

## 🚧 Estado del proyecto

🟡 **En desarrollo**

Proyecto personal orientado a una futura implementación real en centros deportivos.

---

## 👤 Autor

Desarrollado por alumnos de DAW
