<!-- Avis -->
<div class="review">
    <div class="review-main">
        <div class="review-header">
            <img src="<?php echo htmlspecialchars($review->getUser()->getPhoto() ?? ''); ?>" alt="" class="review-img">
            <div class="review-name">
                <h3>
                    <?php
                        echo htmlspecialchars($review->getUser()->getFirstName()) . ' ' .
                            htmlspecialchars($review->getUser()->getLastName()[0] . '.');
                    ?>
                </h3>
                <div class="review-stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fa-solid fa-star <?= $i <= $review->getRating() ? 'score' : '' ?>"></i>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
        <div class="review-message">
            <p>
                <?php echo htmlspecialchars($review->getMessage()); ?>
            </p>
        </div>
    </div>
    <div class="review-footer">
        <p>
            <?php
                $date = $review->getDate();

                $months = [
                    1 => 'janvier',
                    2 => 'février',
                    3 => 'mars',
                    4 => 'avril',
                    5 => 'mai',
                    6 => 'juin',
                    7 => 'juillet',
                    8 => 'août',
                    9 => 'septembre',
                    10 => 'octobre',
                    11 => 'novembre',
                    12 => 'décembre'
                ];

                echo $date->format('j') . ' ' .
                    $months[(int)$date->format('n')] . ' ' .
                    $date->format('Y');
            ?>    
        </p>
        <!-- <a href="#" class="btn body-btn">
            12
            <i class="fa-regular fa-heart"></i>
        </a> -->
    </div>
</div>