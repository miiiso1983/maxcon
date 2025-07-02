import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:permission_handler/permission_handler.dart';
import 'dart:io';

class NotificationService {
  static NotificationService? _instance;
  static NotificationService get instance => _instance ??= NotificationService._();
  
  NotificationService._();

  final FlutterLocalNotificationsPlugin _flutterLocalNotificationsPlugin =
      FlutterLocalNotificationsPlugin();

  Future<void> initialize() async {
    // Android initialization
    const AndroidInitializationSettings initializationSettingsAndroid =
        AndroidInitializationSettings('@mipmap/ic_launcher');

    // iOS initialization
    const DarwinInitializationSettings initializationSettingsIOS =
        DarwinInitializationSettings(
      requestAlertPermission: true,
      requestBadgePermission: true,
      requestSoundPermission: true,
    );

    const InitializationSettings initializationSettings =
        InitializationSettings(
      android: initializationSettingsAndroid,
      iOS: initializationSettingsIOS,
    );

    await _flutterLocalNotificationsPlugin.initialize(
      initializationSettings,
      onDidReceiveNotificationResponse: _onNotificationTapped,
    );

    // Request permissions
    await _requestPermissions();
  }

  Future<void> _requestPermissions() async {
    if (Platform.isAndroid) {
      await Permission.notification.request();
    } else if (Platform.isIOS) {
      await _flutterLocalNotificationsPlugin
          .resolvePlatformSpecificImplementation<
              IOSFlutterLocalNotificationsPlugin>()
          ?.requestPermissions(
            alert: true,
            badge: true,
            sound: true,
          );
    }
  }

  void _onNotificationTapped(NotificationResponse notificationResponse) {
    // Handle notification tap
    print('Notification tapped: ${notificationResponse.payload}');
  }

  Future<void> showInstantNotification({
    required int id,
    required String title,
    required String body,
    String? payload,
  }) async {
    const AndroidNotificationDetails androidPlatformChannelSpecifics =
        AndroidNotificationDetails(
      'sales_rep_channel',
      'Sales Representative Notifications',
      channelDescription: 'Notifications for sales representatives',
      importance: Importance.high,
      priority: Priority.high,
      showWhen: true,
    );

    const DarwinNotificationDetails iOSPlatformChannelSpecifics =
        DarwinNotificationDetails(
      presentAlert: true,
      presentBadge: true,
      presentSound: true,
    );

    const NotificationDetails platformChannelSpecifics = NotificationDetails(
      android: androidPlatformChannelSpecifics,
      iOS: iOSPlatformChannelSpecifics,
    );

    await _flutterLocalNotificationsPlugin.show(
      id,
      title,
      body,
      platformChannelSpecifics,
      payload: payload,
    );
  }

  Future<void> scheduleNotification({
    required int id,
    required String title,
    required String body,
    required DateTime scheduledDate,
    String? payload,
  }) async {
    const AndroidNotificationDetails androidPlatformChannelSpecifics =
        AndroidNotificationDetails(
      'sales_rep_reminders',
      'Sales Representative Reminders',
      channelDescription: 'Scheduled reminders for sales representatives',
      importance: Importance.high,
      priority: Priority.high,
      showWhen: true,
    );

    const DarwinNotificationDetails iOSPlatformChannelSpecifics =
        DarwinNotificationDetails(
      presentAlert: true,
      presentBadge: true,
      presentSound: true,
    );

    const NotificationDetails platformChannelSpecifics = NotificationDetails(
      android: androidPlatformChannelSpecifics,
      iOS: iOSPlatformChannelSpecifics,
    );

    await _flutterLocalNotificationsPlugin.schedule(
      id,
      title,
      body,
      scheduledDate,
      platformChannelSpecifics,
      payload: payload,
      androidScheduleMode: AndroidScheduleMode.exactAllowWhileIdle,
    );
  }

  Future<void> scheduleReminderNotification({
    required int reminderId,
    required String customerName,
    required double amount,
    required DateTime reminderDate,
  }) async {
    await scheduleNotification(
      id: reminderId,
      title: 'تذكير تحصيل',
      body: 'تذكير بتحصيل $amount ريال من العميل $customerName',
      scheduledDate: reminderDate,
      payload: 'reminder_$reminderId',
    );
  }

  Future<void> showVisitReminderNotification({
    required int visitId,
    required String customerName,
    required DateTime visitTime,
  }) async {
    await showInstantNotification(
      id: visitId + 10000, // Offset to avoid conflicts
      title: 'تذكير زيارة',
      body: 'موعد زيارة العميل $customerName في ${_formatTime(visitTime)}',
      payload: 'visit_$visitId',
    );
  }

  Future<void> showOrderCreatedNotification({
    required int orderId,
    required String customerName,
    required double amount,
  }) async {
    await showInstantNotification(
      id: orderId + 20000, // Offset to avoid conflicts
      title: 'تم إنشاء طلب جديد',
      body: 'تم إنشاء طلب بقيمة $amount ريال للعميل $customerName',
      payload: 'order_$orderId',
    );
  }

  Future<void> showPaymentCollectedNotification({
    required int paymentId,
    required String customerName,
    required double amount,
  }) async {
    await showInstantNotification(
      id: paymentId + 30000, // Offset to avoid conflicts
      title: 'تم تحصيل دفعة',
      body: 'تم تحصيل $amount ريال من العميل $customerName',
      payload: 'payment_$paymentId',
    );
  }

  Future<void> cancelNotification(int id) async {
    await _flutterLocalNotificationsPlugin.cancel(id);
  }

  Future<void> cancelAllNotifications() async {
    await _flutterLocalNotificationsPlugin.cancelAll();
  }

  Future<List<PendingNotificationRequest>> getPendingNotifications() async {
    return await _flutterLocalNotificationsPlugin.pendingNotificationRequests();
  }

  String _formatTime(DateTime dateTime) {
    return '${dateTime.hour.toString().padLeft(2, '0')}:${dateTime.minute.toString().padLeft(2, '0')}';
  }

  // Daily reminder for pending tasks
  Future<void> scheduleDailyReminder() async {
    await _flutterLocalNotificationsPlugin.periodicallyShow(
      999999, // Unique ID for daily reminder
      'مهام اليوم',
      'لديك مهام معلقة، تحقق من التطبيق',
      RepeatInterval.daily,
      const NotificationDetails(
        android: AndroidNotificationDetails(
          'daily_reminders',
          'Daily Reminders',
          channelDescription: 'Daily reminders for pending tasks',
          importance: Importance.medium,
          priority: Priority.medium,
        ),
        iOS: DarwinNotificationDetails(
          presentAlert: true,
          presentBadge: true,
          presentSound: true,
        ),
      ),
    );
  }

  Future<void> cancelDailyReminder() async {
    await _flutterLocalNotificationsPlugin.cancel(999999);
  }
}
