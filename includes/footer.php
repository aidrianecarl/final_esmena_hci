<!-- Footer Component -->
<footer class="app-footer">
    <div class="footer-inner">
        <span>&copy; <?php echo date("Y"); ?> <?php echo APP_NAME; ?> — All Rights Reserved.</span>
        <span>Programmed by: <strong>Aidriane Carl Esmeña</strong></span>
    </div>
</footer>

<style>
    .app-footer {
        background: #DC143C;
        color: white;
        padding: 14px 0;
        width: calc(100% - 260px); /* Do not cover sidebar */
        margin-left: 260px;        /* Align beside sidebar */
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
        margin-top: 40px;          /* Space above footer */
    }

    .footer-inner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 32px;
        font-size: 14px;
        font-weight: 500;
    }

    @media(max-width: 991px) {
        .app-footer {
            width: 100%;
            margin-left: 0;
            text-align: center;
        }

        .footer-inner {
            flex-direction: column;
            gap: 6px;
        }
    }
</style>
