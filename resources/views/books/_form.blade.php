@csrf
<div class="form-body">
    <x-input name="title" label="Název" :value="$book->title" required maxlength="255" autofocus />
    <x-input name="author" label="Autor" :value="$book->author" required maxlength="255" />
    <div class="form-row">
        <x-input name="isbn" label="ISBN" :value="$book->isbn" required inputmode="numeric"
                 hint="13 znaků. Pomlčky a mezery se odstraní." />
        <x-input name="available_copies" label="Výtisků v regálu" type="number" min="0" step="1"
                 :value="$book->available_copies ?? 1" required />
    </div>
</div>
