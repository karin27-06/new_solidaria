<template>
    <div class="empty-cart-container">
        <svg
            class="empty-cart-svg"
            xmlns="http://www.w3.org/2000/svg"
            width="180"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <circle cx="9" cy="21" r="1" />
            <circle cx="20" cy="21" r="1" />
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61l1.38-7.39H6" />
        </svg>
        <p class="empty-cart-text pl-5">Tu carrito está vacío</p>
    </div>
</template>

<script setup lang="ts"></script>

<style scoped>
.empty-cart-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem 0;
}
.empty-cart-svg {
    width: 180px;
    height: auto;
    margin-bottom: 1rem;
    opacity: 0.8;
    color: #888;
}
.empty-cart-text {
    font-size: 1.2rem;
    color: #888;
}
</style>
