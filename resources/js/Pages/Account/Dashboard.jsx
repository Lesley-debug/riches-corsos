import { Head, Link, usePage } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';
import AccountNav from '@/Components/AccountNav';

function OrderTracker({ order }) {
  const status = order.status || 'pending';
  
  // Progress stages: 1 = received, 2 = review, 3 = health_prep, 4 = homecoming
  let currentStage = 1;
  if (['reviewing', 'in_review', 'under_review'].includes(status)) currentStage = 2;
  if (['confirmed', 'approved', 'deposit_paid', 'health_prep'].includes(status)) currentStage = 3;
  if (['ready_for_pickup', 'shipping', 'completed', 'delivered'].includes(status)) currentStage = 4;

  const stages = [
    { num: 1, label: 'Request Placed', desc: 'Application received' },
    { num: 2, label: 'Breeder Review', desc: 'Phone consultation' },
    { num: 3, label: 'Health Clearance', desc: 'Vet exam & vaccination' },
    { num: 4, label: 'Homecoming', desc: 'Delivery / pickup ready' },
  ];

  const puppy = order.puppy;
  const image = puppy?.images?.[0]?.path ? `/storage/${puppy.images[0].path}` : '/images/puppy-placeholder.jpg';

  return (
    <div className="order-tracker-card">
      <div className="order-tracker-top">
        <div className="order-tracker-puppy-info">
          <div className="order-tracker-avatar">
            <img src={image} alt={puppy?.name || 'Puppy'} />
          </div>
          <div>
            <div className="order-tracker-name-row">
              <h4 className="order-tracker-puppy-name">{puppy?.name || 'Puppy Reservation'}</h4>
              <span className={`order-status-pill status-${status}`}>{status.replace('_', ' ')}</span>
            </div>
            <p className="order-tracker-meta">
              Order #{order.id} &bull; Requested on {new Date(order.created_at).toLocaleDateString()}
              {puppy?.price && ` \u2022 $${Number(puppy.price).toLocaleString()}`}
            </p>
          </div>
        </div>

        {puppy?.slug && (
          <Link href={`/puppies/${puppy.slug}`} className="order-tracker-link">
            View Puppy &rarr;
          </Link>
        )}
      </div>

      <div className="order-steps-container">
        <div className="order-steps-bar">
          <div 
            className="order-steps-progress" 
            style={{ width: `${((currentStage - 1) / (stages.length - 1)) * 100}%` }} 
          />
        </div>
        <div className="order-steps-list">
          {stages.map((stage) => {
            const isComplete = currentStage > stage.num;
            const isCurrent = currentStage === stage.num;
            return (
              <div 
                key={stage.num} 
                className={`order-step-item ${isComplete ? 'is-complete' : ''} ${isCurrent ? 'is-current' : ''}`}
              >
                <div className="order-step-circle">
                  {isComplete ? (
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" width="14" height="14">
                      <polyline points="20 6 9 17 4 12" />
                    </svg>
                  ) : (
                    <span>{stage.num}</span>
                  )}
                </div>
                <div className="order-step-label">{stage.label}</div>
                <div className="order-step-desc">{stage.desc}</div>
              </div>
            );
          })}
        </div>
      </div>
    </div>
  );
}

export default function Dashboard({ 
  recentOrders = [], 
  orders = [],
  stats = {}, 
  recentNotifications = [], 
  memberSince = 'Member' 
}) {
  const { props } = usePage();
  const user = props.auth?.user;

  const totalOrders = stats.total_orders ?? recentOrders.length;
  const wishlistCount = stats.wishlist_count ?? props.wishlistCount ?? 0;
  const unreadCount = stats.unread_notifications ?? props.unreadNotificationsCount ?? 0;

  return (
    <SiteLayout>
      <Head title="My Account — Riches Corsos" />

      <div className="account-grid">
        <AccountNav />

        <div className="account-content">
          {/* Welcome Header */}
          <div className="account-welcome-banner">
            <div className="welcome-avatar-circle">
              {user?.name ? user.name.charAt(0).toUpperCase() : 'C'}
            </div>
            <div className="welcome-details">
              <span className="welcome-role-tag">VIP Client &bull; {memberSince}</span>
              <h1 className="welcome-heading">Welcome back, {user?.name?.split(' ')[0]}!</h1>
              <p className="welcome-subtext">
                Track your active Cane Corso puppy reservations, health clearances, and litter notifications.
              </p>
            </div>
            <div className="welcome-cta">
              <Link href="/puppies" className="btn-solid">
                Browse Puppies
              </Link>
            </div>
          </div>

          {/* Quick Stats Grid */}
          <div className="account-stats-grid">
            <Link href="/orders" className="stat-box-card">
              <div className="stat-box-icon stat-box-icon--orders">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" width="22" height="22">
                  <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
                  <line x1="3" y1="6" x2="21" y2="6" />
                  <path d="M16 10a4 4 0 0 1-8 0" />
                </svg>
              </div>
              <div className="stat-box-data">
                <div className="stat-box-num">{totalOrders}</div>
                <div className="stat-box-label">Reservations</div>
              </div>
            </Link>

            <Link href="/wishlist" className="stat-box-card">
              <div className="stat-box-icon stat-box-icon--wishlist">
                <svg viewBox="0 0 24 24" fill="currentColor" width="22" height="22">
                  <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                </svg>
              </div>
              <div className="stat-box-data">
                <div className="stat-box-num">{wishlistCount}</div>
                <div className="stat-box-label">Saved Puppies</div>
              </div>
            </Link>

            <Link href="/account/notifications" className="stat-box-card">
              <div className="stat-box-icon stat-box-icon--notif">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" width="22" height="22">
                  <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                  <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                </svg>
              </div>
              <div className="stat-box-data">
                <div className="stat-box-num">{unreadCount}</div>
                <div className="stat-box-label">New Alerts</div>
              </div>
            </Link>
          </div>

          {/* Active Reservations & Tracking Section */}
          <div className="account-section-card">
            <div className="section-title-row">
              <div>
                <h2 className="section-title">Active Reservations & Tracking</h2>
                <p className="section-subtitle">Real-time status of your puppy requests and adoption milestones.</p>
              </div>
              {recentOrders.length > 0 && (
                <Link href="/orders" className="section-header-link">
                  View all ({totalOrders}) &rarr;
                </Link>
              )}
            </div>

            {recentOrders.length > 0 ? (
              <div className="orders-tracker-stack">
                {recentOrders.map((order) => (
                  <OrderTracker key={order.id} order={order} />
                ))}
              </div>
            ) : (
              <div className="empty-journey-card">
                <div className="empty-journey-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" width="32" height="32">
                    <circle cx="12" cy="12" r="10" />
                    <path d="M8 14s1.5 2 4 2 4-2 4-2" />
                    <line x1="9" y1="9" x2="9.01" y2="9" />
                    <line x1="15" y1="9" x2="15.01" y2="9" />
                  </svg>
                </div>
                <h3>Ready to find your companion?</h3>
                <p>
                  You haven't reserved a puppy yet. Browse our AKC registered champion Cane Corso puppies
                  backed by comprehensive health clearances and lifetime breeder support.
                </p>
                <div className="empty-journey-actions">
                  <Link href="/puppies" className="btn-solid">
                    View Available Puppies
                  </Link>
                  <Link href="/about" className="btn-secondary">
                    Our Breeding Program
                  </Link>
                </div>
              </div>
            )}
          </div>

          {/* Recent Notifications Stream */}
          <div className="account-section-card">
            <div className="section-title-row">
              <div>
                <h2 className="section-title">Recent Activity & Alerts</h2>
                <p className="section-subtitle">Updates on your reservations, wishlist, and newly posted puppies.</p>
              </div>
              <Link href="/account/notifications" className="section-header-link">
                View all notifications &rarr;
              </Link>
            </div>

            {recentNotifications.length > 0 ? (
              <div className="dashboard-activity-list">
                {recentNotifications.slice(0, 4).map((notif) => (
                  <div key={notif.id} className="dashboard-activity-item">
                    <div className="activity-dot-line">
                      <div className={`activity-dot ${notif.read ? 'is-read' : 'is-unread'}`} />
                    </div>
                    <div className="activity-content">
                      <div className="activity-header">
                        <span className="activity-title">{notif.title}</span>
                        <span className="activity-time">{notif.created_at}</span>
                      </div>
                      <p className="activity-message">{notif.message}</p>
                      {notif.action_url && (
                        <Link href={notif.action_url} className="activity-link">
                          Explore &rarr;
                        </Link>
                      )}
                    </div>
                  </div>
                ))}
              </div>
            ) : (
              <p style={{ color: 'var(--stone)', fontSize: 14 }}>
                No recent activity. As you reserve puppies or save favorites, your updates will show here.
              </p>
            )}
          </div>

          {/* Customer Assurance Perks */}
          <div className="dashboard-perks-row">
            <div className="perk-card">
              <div className="perk-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" width="20" height="20">
                  <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                </svg>
              </div>
              <div>
                <h4>2-Year Health Guarantee</h4>
                <p>Comprehensive coverage against genetic and congenital conditions.</p>
              </div>
            </div>

            <div className="perk-card">
              <div className="perk-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" width="20" height="20">
                  <rect x="1" y="3" width="15" height="13" />
                  <polygon points="16 8 20 8 23 11 23 16 16 16 16 8" />
                  <circle cx="5.5" cy="18.5" r="2.5" />
                  <circle cx="18.5" cy="18.5" r="2.5" />
                </svg>
              </div>
              <div>
                <h4>White-Glove Delivery</h4>
                <p>Safe, temperature-regulated flight nanny or ground transport to your door.</p>
              </div>
            </div>

            <div className="perk-card">
              <div className="perk-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" width="20" height="20">
                  <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
                </svg>
              </div>
              <div>
                <h4>Lifetime Breeder Support</h4>
                <p>Direct access to our certified breeders for training and nutritional advice.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </SiteLayout>
  );
}
