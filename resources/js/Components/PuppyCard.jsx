import { Link, router, usePage } from '@inertiajs/react';
import { useState } from 'react';

function ageLabel(weeks) {
    if (weeks == null) {
        return null;
    }

    if (weeks < 4) {
        return `${weeks} week${weeks === 1 ? '' : 's'}`;
    }

    if (weeks < 52) {
        const months = Math.round(weeks / 4.33);

        return `${months} month${months === 1 ? '' : 's'}`;
    }

    const years = Math.floor(weeks / 52);

    return `${years} year${years === 1 ? '' : 's'}`;
}

function CartIcon() {
    return (
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" strokeWidth="1.7" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
            <path d="M3 4h2l1 12h13l2-9H7" />
            <circle cx="9" cy="20" r="1.4" fill="currentColor" stroke="none" />
            <circle cx="18" cy="20" r="1.4" fill="currentColor" stroke="none" />
        </svg>
    );
}

function HeartIcon({ filled }) {
    return (
        <svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true">
            <path
                d="M12 21s-7-4.5-9.3-8.8C1.2 8.6 2.8 5 6.3 5c2 0 3.3 1.1 4 2.1.7-1 2-2.1 4-2.1 3.5 0 5.1 3.6 3.6 7.2C19 16.5 12 21 12 21z"
                stroke="currentColor"
                strokeWidth="1.6"
                fill={filled ? 'currentColor' : 'none'}
            />
        </svg>
    );
}

export default function PuppyCard({ puppy, wishlisted = false }) {
    const { props } = usePage();
    const user = props.auth?.user;
    const wishlistPuppyIds = props.wishlistPuppyIds ?? [];
    const cartPuppyIds = props.cartPuppyIds ?? [];

    const isWishlisted = wishlistPuppyIds.includes(puppy.id) || wishlisted;
    const isInCart = cartPuppyIds.includes(puppy.id);

    const cover = puppy.images?.[0]?.path;
    const age = ageLabel(puppy.age_in_weeks);
    const gender = puppy.sex === 'male' ? 'Male' : (puppy.sex === 'female' ? 'Female' : (puppy.sex ?? ''));
    const subtitle = [gender, age, puppy.color].filter(Boolean).join(' - ');

    const [notice, setNotice] = useState('');

    const showNotice = (message) => {
        setNotice(message);
        window.setTimeout(() => setNotice(''), 2600);
    };

    const handleWishlist = (event) => {
        event.preventDefault();

        if (!user) {
            router.visit('/login');
            return;
        }

        const willAdd = !isWishlisted;

        router.post('/wishlist/toggle', { puppy_id: puppy.id }, {
            preserveScroll: true,
            onSuccess: () => {
                showNotice(willAdd ? `You like ${puppy.name}! Added to wishlist.` : `Removed ${puppy.name} from your wishlist.`);
            },
        });
    };

    const handleCart = (event) => {
        event.preventDefault();

        if (!user) {
            router.visit('/login');
            return;
        }

        if (isInCart) {
            showNotice(`${puppy.name} is already in your cart.`);
            return;
        }

        router.post('/cart', { puppy_id: puppy.id }, {
            preserveScroll: true,
            onSuccess: () => {
                showNotice(`${puppy.name} added to your cart.`);
            },
        });
    };

    return (
        <article className="pcard">
            <Link href={`/puppies/${puppy.slug}`} className="pcard-img-wrap">
                {cover
                    ? <img src={`/storage/${cover}`} alt={puppy.name} className="pcard-img" loading="lazy" />
                    : <div className="pcard-img-placeholder" />
                }
            </Link>

            <div className="pcard-body">
                {subtitle && <p className="pcard-subtitle">{subtitle}</p>}
                <Link href={`/puppies/${puppy.slug}`} className="pcard-name">{puppy.name}</Link>

                <div className="pcard-footer">
                    {puppy.price ? (
                        <span className="pcard-price">${Number(puppy.price).toLocaleString()}</span>
                    ) : <span />}
                    <div className="pcard-footer-actions">
                        <button
                            type="button"
                            className={`pcard-icon-btn ${isInCart ? 'pcard-icon-btn--active' : ''}`}
                            onClick={handleCart}
                            aria-label={isInCart ? `${puppy.name} in cart` : `Add ${puppy.name} to cart`}
                            title={isInCart ? 'In cart' : 'Add to cart'}
                        >
                            <CartIcon />
                        </button>
                        <button
                            type="button"
                            className={`pcard-icon-btn pcard-wishlist ${isWishlisted ? 'pcard-wishlist--active' : ''}`}
                            onClick={handleWishlist}
                            aria-label={isWishlisted ? `Remove ${puppy.name} from wishlist` : `Add ${puppy.name} to wishlist`}
                            title={isWishlisted ? 'Liked' : 'Add to wishlist'}
                        >
                            <HeartIcon filled={isWishlisted} />
                        </button>
                        <Link href={`/puppies/${puppy.slug}`} className="pcard-view-btn">
                            View Details
                        </Link>
                    </div>
                </div>

                {notice && <p className="pcard-notice" role="status">{notice}</p>}
            </div>
        </article>
    );
}
