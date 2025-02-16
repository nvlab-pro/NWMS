<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.accordion .collapse.show').forEach(collapse => {
            collapse.classList.remove('show'); // Убираем класс "show" для закрытия
        });

        document.querySelectorAll('.accordion .accordion-heading').forEach(heading => {
            heading.classList.add('collapsed'); // Добавляем класс "collapsed"
            heading.setAttribute('aria-expanded', 'false'); // Обновляем атрибут
        });
    });
</script>

