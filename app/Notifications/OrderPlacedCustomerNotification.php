<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlacedCustomerNotification extends Notification
{
    use Queueable;

    /** @param list<Order> $orders */
    public function __construct(
        public Order $order,
        public array $orders = [],
        public string $paymentMethodLabel = '',
    ) {}

    public function via(object $notifiable): array
    {
        // Only database channel if notifiable is a User (not an anonymous route)
        if ($notifiable instanceof User) {
            return ['database', 'mail'];
        }

        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $allOrders = ! empty($this->orders) ? $this->orders : [$this->order];
        $puppyNames = collect($allOrders)->map(fn (Order $o) => $o->puppy?->name ?? 'Puppy')->join(', ', ' & ');
        $totalAmount = collect($allOrders)->sum(fn (Order $o) => (float) ($o->puppy?->price ?? 0));
        $orderIds = collect($allOrders)->map(fn (Order $o) => '#'.$o->id)->join(', ');
        $buyerName = $this->order->buyer_name;
        $buyerEmail = $this->order->buyer_email;

        $mail = (new MailMessage)
            ->from('info@richescorsos.com', 'Riches Corsos')
            ->subject('🐾 Reservation Confirmed — '.$puppyNames.' | Riches Corsos')
            ->greeting('Hello '.$buyerName.'! 🎉')
            ->line('**Thank you for your reservation at Riches Corsos!** We are thrilled you have chosen to bring a champion-line Cane Corso into your family.')
            ->line('---')
            ->line('**📋 Order Summary**')
            ->line('• **Puppy / Puppies:** '.$puppyNames)
            ->line('• **Order ID(s):** '.$orderIds)
            ->line('• **Total Value:** $'.number_format($totalAmount, 2))
            ->line('• **Payment Method Selected:** '.($this->paymentMethodLabel ?: 'Not specified'))
            ->line('• **Order Status:** Under Review')
            ->line('---')
            ->line('**💳 Next Steps — Payment**')
            ->line('No payment has been charged yet. Our team will contact you within **24 hours** via email or phone to:')
            ->line('1. Confirm your reservation and puppy availability')
            ->line('2. Send you the full payment instructions for your selected method ('.($this->paymentMethodLabel ?: 'your selected method').')')
            ->line('3. Walk you through the deposit and delivery process')
            ->line('---')
            ->line('**🚚 Delivery**')
            ->line('We offer white-glove delivery nationwide. Our team will arrange safe, comfortable transport for your puppy directly to your door or a nearby airport pickup.')
            ->line('---')
            ->line('**📞 Questions?**')
            ->line('Reply to this email or contact us directly:')
            ->line('• **Email:** info@richescorsos.com')
            ->line('• **Phone:** +1 (214) 212-3023')
            ->line('• **WhatsApp:** Available on our website')
            ->action('View Your Order Status', url('/orders'))
            ->line('We look forward to welcoming your new family member home. 🐾')
            ->salutation('With love, **The Riches Corsos Team**');

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        $puppy = $this->order->puppy;

        return [
            'type' => 'order_placed',
            'title' => 'Reservation Placed: '.($puppy?->name ?? 'Puppy'),
            'message' => 'Your reservation for '.($puppy?->name ?? 'your puppy').' was placed. We will contact you within 24 hours.',
            'order_id' => $this->order->id,
            'puppy_id' => $this->order->puppy_id,
            'puppy_name' => $puppy?->name ?? 'Puppy',
            'action_url' => '/orders',
            'icon' => 'bag',
        ];
    }
}
