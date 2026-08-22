<?php
/**
 * UpaKo - Footer Include
 */
?>
    <!-- Footer -->
    <footer class="bg-dark text-light mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-building"></i> UpaKo</h5>
                    <p class="small">Your Property, Your Rent, Your Records.</p>
                    <p class="small text-muted">© 2024 UpaKo. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="small">
                        <a href="#" class="text-light text-decoration-none me-3">Privacy Policy</a>
                        <a href="#" class="text-light text-decoration-none me-3">Terms of Service</a>
                        <a href="#" class="text-light text-decoration-none">Contact Us</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
    
    <script>
        // Auto-hide alerts after 5 seconds
        $(document).ready(function() {
            $('.alert:not(.alert-permanent)').delay(5000).fadeOut('slow', function() {
                $(this).remove();
            });
        });
    </script>
</body>
</html>
