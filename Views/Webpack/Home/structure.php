<div class="container mt-5">
    <div class="accordion" id="accordionExample">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                        aria-expanded="true" aria-controls="collapseOne">
                    Elemento del Acordeón #1
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    Este es el contenido del primer elemento del acordeón.
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    Elemento del Acordeón #2
                </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    Este es el contenido del segundo elemento del acordeón.
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    Elemento del Acordeón #3
                </button>
            </h2>
            <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    Este es el contenido del tercer elemento del acordeón.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle con Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Sortable.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var accordion = document.getElementById('accordionExample');

        // Inicializar Sortable
        new Sortable(accordion, {
            animation: 150,
            ghostClass: 'sortable-ghost'
        });

        // Manejar transiciones suaves
        var myCollapsible = document.querySelectorAll('.accordion-collapse');
        myCollapsible.forEach(collapse => {
            collapse.addEventListener('show.bs.collapse', function () {
                this.style.height = this.scrollHeight + 'px';
            });
            collapse.addEventListener('hide.bs.collapse', function () {
                this.style.height = '0px';
            });
        });
    });
</script>

<style>
    .accordion-button:not(.collapsed)::after {
        transform: rotate(-180deg);
    }

    .accordion-button::after {
        transition: transform 0.3s ease-in-out;
    }

    .accordion-collapse {
        transition: height 0.3s ease-in-out !important;
    }
</style>