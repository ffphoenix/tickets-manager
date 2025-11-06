# 🗣️ Ubiquitous Language per Context

This document defines the **Ubiquitous Language** for each **bounded context** in the Ticket Management and Selling System.

Using a consistent domain language ensures shared understanding across teams — developers, business analysts, and stakeholders — and avoids ambiguity between contexts.

---

## 🧭 Core Subdomain: Ticketing & Order Management

### **Event Catalog Context**

| Term | Meaning |
|------|----------|
| **Event** | A planned activity such as a concert, show, or match. |
| **Schedule** | The date and time when the event takes place. |
| **Organizer** | The entity (person or company) responsible for creating and managing events. |
| **Category** | Type of event (Music, Sports, Theatre, etc.). |
| **Status** | Lifecycle stage of the event — `Draft`, `Published`, or `Cancelled`. |

✅ **Example Phrases:** “Publish event,” “List active events,” “Cancel event.”

---

### **Venue Context**

| Term | Meaning |
|------|----------|
| **Venue** | The physical or virtual location where an event occurs. |
| **Seat Map** | A layout defining seat positions and arrangements. |
| **Section** | A grouping of seats within a venue (Balcony, VIP, etc.). |
| **Seat** | A unique seat identifier. |
| **Capacity** | The total number of attendees that can be accommodated. |

✅ **Example Phrases:** “Define venue layout,” “Add section,” “Update seat map.”

---

### **Ticket Inventory Context**

| Term | Meaning |
|------|----------|
| **Ticket Type** | Defines attributes of a ticket (e.g., VIP, Early Bird). |
| **Batch** | A group of tickets created with the same rules or price. |
| **Availability** | Number of unsold tickets remaining. |
| **Price Tier** | Pricing structure based on category or time. |
| **Allocation** | Distribution of tickets to sections, channels, or partners. |

✅ **Example Phrases:** “Allocate tickets,” “Update pricing,” “Release batch.”

---

### **Reservation Context**

| Term | Meaning |
|------|----------|
| **Reservation** | Temporary hold on tickets by a user before purchase. |
| **Reservation ID** | Unique identifier for a reservation. |
| **Hold Timer** | Duration that a reservation remains valid. |
| **Expiration Policy** | Rules that determine when and how holds are released. |

✅ **Example Phrases:** “Reserve seats,” “Release expired reservations,” “Extend reservation.”

---

### **Order Management Context**

| Term | Meaning |
|------|----------|
| **Order** | A confirmed collection of tickets purchased by a user. |
| **Order Line** | Individual ticket or group of tickets within an order. |
| **Cart** | Temporary container for selected tickets prior to checkout. |
| **Order Status** | Current state of an order — `Pending`, `Paid`, `Cancelled`, `Completed`. |

✅ **Example Phrases:** “Add to cart,” “Confirm order,” “Cancel order.”

---

### **Payment Context**

| Term | Meaning |
|------|----------|
| **Payment** | A financial transaction made to complete an order. |
| **Payment Provider** | External processor (e.g., Stripe, PayPal). |
| **Transaction ID** | Unique identifier of a processed payment. |
| **Payment Status** | Status of the transaction — `Initiated`, `Successful`, `Failed`, `Refunded`. |
| **Refund** | Reversal of a previously successful payment. |

✅ **Example Phrases:** “Initiate payment,” “Confirm transaction,” “Issue refund.”

---

### **Ticket Delivery Context**

| Term | Meaning |
|------|----------|
| **Digital Ticket** | A digital representation of a purchased ticket (e.g., QR code, PDF, or wallet pass). |
| **Delivery Channel** | Method used to send tickets (Email, App, Mobile Wallet). |
| **Delivery Status** | Indicates ticket delivery progress — `Pending`, `Delivered`, `Failed`. |
| **Validation Code** | Unique code used for entry verification at the venue. |

✅ **Example Phrases:** “Send ticket via email,” “Mark as delivered,” “Validate ticket.”

---

## 🧑‍💻 Supporting Subdomain: System Users

### **Identity Context**

| Term | Meaning |
|------|----------|
| **User Account** | A registered identity capable of logging into the system. |
| **Authentication** | The process of verifying user credentials. |
| **Credential** | Password, token, or external identity proof (OAuth). |
| **Session** | The active, authenticated state of a user within the system. |

✅ **Example Phrases:** “Register user,” “Authenticate session,” “Reset password.”

---

### **Access Control Context**

| Term | Meaning |
|------|----------|
| **Role** | A collection of permissions defining what actions a user can perform (e.g., Customer, Organizer, Admin). |
| **Permission** | A specific authorized action (e.g., `CreateEvent`, `ViewReports`). |
| **Authorization** | The process of verifying a user’s access to a specific action or resource. |

✅ **Example Phrases:** “Assign role,” “Check permission,” “Authorize request.”

---

### **Profile Context**

| Term | Meaning |
|------|----------|
| **User Profile** | Personal and contact details linked to a user account. |
| **Preference** | User-defined settings such as language, notifications, or currency. |
| **Contact Info** | Email address, phone number, or connected social account. |
| **Linked Account** | Integration with external authentication providers (Google, Facebook, etc.). |

✅ **Example Phrases:** “Update profile,” “Change language,” “Link account.”

---

## 📊 Generic Subdomain: Analytics

### **Analytics Context**

| Term | Meaning |
|------|----------|
| **Report** | Aggregated summary of event, sales, or usage data. |
| **Metric** | Quantitative measure (e.g., tickets sold, total revenue). |
| **KPI** | Key Performance Indicator used for tracking success. |
| **Dashboard** | Visual presentation of metrics and reports. |

✅ **Example Phrases:** “Generate event report,” “Track daily sales,” “View performance dashboard.”

---

## 🤝 Generic Subdomain: Customer Support

### **Support Context**

| Term | Meaning |
|------|----------|
| **Support Case** | Logged customer issue or inquiry. |
| **Refund Request** | Request initiated by a customer to reverse a payment. |
| **Reissue Request** | Customer request for a replacement ticket. |
| **Agent** | Support staff handling customer cases. |
| **Resolution** | The outcome of a support case (`Resolved`, `Rejected`, `Pending`). |

✅ **Example Phrases:** “Open support case,” “Approve refund,” “Reissue ticket.”

---

## 🧩 Notes

- **System Users** is an upstream provider of identity and authorization to all other contexts.
- **Ticketing & Order Management** is the **core domain** — it contains the system’s main business logic and rules.
- **Analytics** and **Customer Support** are **downstream consumers** that derive insights and handle post-sale operations.
- Consistent use of these terms in code, documentation, and communication is critical for effective collaboration.

---

📄 *Maintained as part of the Domain-Driven Design (DDD) architecture documentation for the Ticket Management and Selling System.*
