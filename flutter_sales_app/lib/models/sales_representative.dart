import 'package:json_annotation/json_annotation.dart';

part 'sales_representative.g.dart';

@JsonSerializable()
class SalesRepresentative {
  final int id;
  final String name;
  final String email;
  final String phone;
  final String employeeCode;
  final String? profileImage;
  final List<String> assignedAreas;
  final String status;
  final double baseSalary;
  final double commissionRate;
  final DateTime hireDate;

  SalesRepresentative({
    required this.id,
    required this.name,
    required this.email,
    required this.phone,
    required this.employeeCode,
    this.profileImage,
    required this.assignedAreas,
    required this.status,
    required this.baseSalary,
    required this.commissionRate,
    required this.hireDate,
  });

  factory SalesRepresentative.fromJson(Map<String, dynamic> json) =>
      _$SalesRepresentativeFromJson(json);

  Map<String, dynamic> toJson() => _$SalesRepresentativeToJson(this);

  bool get isActive => status == 'active';
}

@JsonSerializable()
class Customer {
  final int id;
  final String name;
  final String email;
  final String phone;
  final String address;
  final String customerCode;
  final String customerType;
  final double creditLimit;
  final double currentBalance;
  final String status;

  Customer({
    required this.id,
    required this.name,
    required this.email,
    required this.phone,
    required this.address,
    required this.customerCode,
    required this.customerType,
    required this.creditLimit,
    required this.currentBalance,
    required this.status,
  });

  factory Customer.fromJson(Map<String, dynamic> json) =>
      _$CustomerFromJson(json);

  Map<String, dynamic> toJson() => _$CustomerToJson(this);
}

@JsonSerializable()
class Product {
  final int id;
  final String name;
  final String code;
  final String description;
  final double price;
  final String unit;
  final int stockQuantity;
  final String? imageUrl;
  final String category;
  final bool isActive;

  Product({
    required this.id,
    required this.name,
    required this.code,
    required this.description,
    required this.price,
    required this.unit,
    required this.stockQuantity,
    this.imageUrl,
    required this.category,
    required this.isActive,
  });

  factory Product.fromJson(Map<String, dynamic> json) =>
      _$ProductFromJson(json);

  Map<String, dynamic> toJson() => _$ProductToJson(this);
}

@JsonSerializable()
class CustomerVisit {
  final int? id;
  final int salesRepresentativeId;
  final int customerId;
  final DateTime visitDate;
  final double? latitude;
  final double? longitude;
  final String? locationAddress;
  final String visitType;
  final String visitStatus;
  final String? visitPurpose;
  final String? visitNotes;
  final String? customerFeedback;
  final List<String>? visitPhotos;
  final DateTime? checkInTime;
  final DateTime? checkOutTime;
  final int? durationMinutes;
  final bool orderCreated;
  final bool paymentCollected;

  CustomerVisit({
    this.id,
    required this.salesRepresentativeId,
    required this.customerId,
    required this.visitDate,
    this.latitude,
    this.longitude,
    this.locationAddress,
    required this.visitType,
    required this.visitStatus,
    this.visitPurpose,
    this.visitNotes,
    this.customerFeedback,
    this.visitPhotos,
    this.checkInTime,
    this.checkOutTime,
    this.durationMinutes,
    this.orderCreated = false,
    this.paymentCollected = false,
  });

  factory CustomerVisit.fromJson(Map<String, dynamic> json) =>
      _$CustomerVisitFromJson(json);

  Map<String, dynamic> toJson() => _$CustomerVisitToJson(this);
}

@JsonSerializable()
class SalesOrder {
  final int? id;
  final String? orderNumber;
  final int salesRepresentativeId;
  final int customerId;
  final int? customerVisitId;
  final DateTime orderDate;
  final DateTime? deliveryDate;
  final String status;
  final double subtotal;
  final double discountAmount;
  final double discountPercentage;
  final double taxAmount;
  final double taxPercentage;
  final double shippingCost;
  final double totalAmount;
  final String currency;
  final String paymentTerms;
  final String? notes;
  final String? internalNotes;
  final Map<String, dynamic>? deliveryAddress;
  final String priority;
  final List<SalesOrderItem> items;

  SalesOrder({
    this.id,
    this.orderNumber,
    required this.salesRepresentativeId,
    required this.customerId,
    this.customerVisitId,
    required this.orderDate,
    this.deliveryDate,
    required this.status,
    required this.subtotal,
    this.discountAmount = 0,
    this.discountPercentage = 0,
    this.taxAmount = 0,
    this.taxPercentage = 0,
    this.shippingCost = 0,
    required this.totalAmount,
    this.currency = 'SAR',
    this.paymentTerms = 'cash',
    this.notes,
    this.internalNotes,
    this.deliveryAddress,
    this.priority = 'normal',
    required this.items,
  });

  factory SalesOrder.fromJson(Map<String, dynamic> json) =>
      _$SalesOrderFromJson(json);

  Map<String, dynamic> toJson() => _$SalesOrderToJson(this);
}

@JsonSerializable()
class SalesOrderItem {
  final int? id;
  final int? salesOrderId;
  final int itemId;
  final String itemName;
  final String? itemCode;
  final String? itemDescription;
  final double quantity;
  final String unit;
  final double unitPrice;
  final double discountAmount;
  final double discountPercentage;
  final double lineTotal;
  final String? notes;

  SalesOrderItem({
    this.id,
    this.salesOrderId,
    required this.itemId,
    required this.itemName,
    this.itemCode,
    this.itemDescription,
    required this.quantity,
    this.unit = 'piece',
    required this.unitPrice,
    this.discountAmount = 0,
    this.discountPercentage = 0,
    required this.lineTotal,
    this.notes,
  });

  factory SalesOrderItem.fromJson(Map<String, dynamic> json) =>
      _$SalesOrderItemFromJson(json);

  Map<String, dynamic> toJson() => _$SalesOrderItemToJson(this);
}

@JsonSerializable()
class PaymentCollection {
  final int? id;
  final String? collectionNumber;
  final int salesRepresentativeId;
  final int customerId;
  final int? customerVisitId;
  final int? invoiceId;
  final DateTime collectionDate;
  final double amount;
  final String currency;
  final String paymentMethod;
  final String? referenceNumber;
  final String? bankName;
  final DateTime? checkDate;
  final String status;
  final String? notes;
  final List<String>? receiptPhotos;
  final double? latitude;
  final double? longitude;

  PaymentCollection({
    this.id,
    this.collectionNumber,
    required this.salesRepresentativeId,
    required this.customerId,
    this.customerVisitId,
    this.invoiceId,
    required this.collectionDate,
    required this.amount,
    this.currency = 'SAR',
    required this.paymentMethod,
    this.referenceNumber,
    this.bankName,
    this.checkDate,
    this.status = 'pending',
    this.notes,
    this.receiptPhotos,
    this.latitude,
    this.longitude,
  });

  factory PaymentCollection.fromJson(Map<String, dynamic> json) =>
      _$PaymentCollectionFromJson(json);

  Map<String, dynamic> toJson() => _$PaymentCollectionToJson(this);
}

@JsonSerializable()
class CollectionReminder {
  final int? id;
  final int salesRepresentativeId;
  final int customerId;
  final int? invoiceId;
  final String reminderTitle;
  final String? reminderDescription;
  final DateTime reminderDate;
  final DateTime? reminderTime;
  final double? amountToCollect;
  final String priority;
  final String status;
  final DateTime? notifiedAt;
  final DateTime? completedAt;
  final String? completionNotes;
  final List<String>? notificationMethods;
  final bool autoCreated;

  CollectionReminder({
    this.id,
    required this.salesRepresentativeId,
    required this.customerId,
    this.invoiceId,
    required this.reminderTitle,
    this.reminderDescription,
    required this.reminderDate,
    this.reminderTime,
    this.amountToCollect,
    this.priority = 'normal',
    this.status = 'pending',
    this.notifiedAt,
    this.completedAt,
    this.completionNotes,
    this.notificationMethods,
    this.autoCreated = false,
  });

  factory CollectionReminder.fromJson(Map<String, dynamic> json) =>
      _$CollectionReminderFromJson(json);

  Map<String, dynamic> toJson() => _$CollectionReminderToJson(this);
}

@JsonSerializable()
class Invoice {
  final int id;
  final String invoiceNumber;
  final int customerId;
  final int salesRepresentativeId;
  final DateTime invoiceDate;
  final DateTime? dueDate;
  final double subtotal;
  final double taxAmount;
  final double discountAmount;
  final double totalAmount;
  final double paidAmount;
  final double remainingAmount;
  final String status;
  final String currency;
  final String? notes;
  final String? pdfUrl;

  Invoice({
    required this.id,
    required this.invoiceNumber,
    required this.customerId,
    required this.salesRepresentativeId,
    required this.invoiceDate,
    this.dueDate,
    required this.subtotal,
    required this.taxAmount,
    required this.discountAmount,
    required this.totalAmount,
    required this.paidAmount,
    required this.remainingAmount,
    required this.status,
    this.currency = 'SAR',
    this.notes,
    this.pdfUrl,
  });

  factory Invoice.fromJson(Map<String, dynamic> json) =>
      _$InvoiceFromJson(json);

  Map<String, dynamic> toJson() => _$InvoiceToJson(this);

  bool get isPaid => status == 'paid';
  bool get isPartiallyPaid => status == 'partially_paid';
  bool get isUnpaid => status == 'unpaid';
}
