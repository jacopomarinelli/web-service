from django.db import models

# Create your models here.

# modello corrispondente a tabella Cliente
class Cliente(models.Model):
    codice = models.CharField(
        max_length=6,
        unique=True,
        primary_key=True,
        null=False
    )
    codice_fiscale = models.CharField(
        max_length=16,
        unique=True
    )
    ragione_sociale = models.CharField(
        max_length=50
    )
    indirizzo = models.CharField(
        max_length=27
    )
    città = models.CharField(
        max_length=15
    )

# modello corrispondente a tabella Fattura
class Fattura(models.Model):
    numero = models.CharField(
        max_length=13,
        unique=True,
        primary_key=True,
        null=False
    )
    data = models.CharField(
        max_length=10
    )
    imponibile = models.DecimalField(
        max_digits = 7,
        decimal_places = 2
    )
    iva = models.DecimalField(
        max_digits = 6,
        decimal_places = 2
    )
    totale = models.DecimalField(
        max_digits = 7,
        decimal_places = 2
    )

# modello corrispondente a tabella Utenza
class Utenza(models.Model):
    codice = models.PositiveIntegerField(
        unique=True,
        primary_key=True,
        null=False
    )
    codice_cliente = models.ForeignKey(
        Cliente,
        on_delete=models.PROTECT
    )
    indirizzo = models.CharField(
        max_length=27
    )
    città = models.CharField(
        max_length=15
    )
    stato = models.CharField(
        max_length=8
    )
    data_apertura = models.CharField(
        max_length=10
    )
    data_chiusura = models.CharField(
        max_length=10,
        blank=True
    )
    
# modello corrispondente a tabella Lettura
class Lettura(models.Model):
    numero = models.PositiveIntegerField()
    utenza = models.ForeignKey(
        Utenza,
        on_delete=models.PROTECT,
    )
    fattura = models.ForeignKey(
        Fattura,
        on_delete=models.PROTECT,
        null=True,
        blank=True
    )
    data = models.CharField(max_length=10)
    valore = models.PositiveIntegerField()

    class Meta:
        #numero e utenza di lettura e' unica nel DB
        constraints = [models.UniqueConstraint(fields=['numero', 'utenza'], name='unique_lettura_utenza')]
