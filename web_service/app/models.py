from django.db import models
from django.core.validators import RegexValidator

# Create your models here.

# validatori per formato campi
codice_cliente_validator = RegexValidator(
    regex=r'^[A-Z]{3}\d{3}$',
    message="Formato corretto: 3 lettere seguite da 3 cifre"
)
codice_fiscale_validator = RegexValidator(
    regex=r'^[A-Z]{6}\d{2}[A-Z]\d{2}[A-Z]\d{3}[A-Z]$',
    message="Formato corretto: codice fiscale"
)
numero_fattura_validator = RegexValidator(
    regex=r'^FT-\d{4}-\d{5}$',
    message="Formato corretto: FT-anno-numero"
)
stringa_data_validator = RegexValidator(
    regex=r'^(0[1-9]|[12]\d|3[01])/(0[1-9]|1[0-2])/\d{4}$',
    message="Formato atteso: gg/mm/aaaa",
)
codice_numerico_validator = RegexValidator(
    regex=r'^\d{8}$',
    message="Formato corretto: 8 cifre"
)

# modello corrispondente a tabella Cliente
class Cliente(models.Model):
    codice = models.CharField(
        max_length=6,
        unique=True,
        primary_key=True,
        null=False,
        validators=[codice_cliente_validator]
    )
    codice_fiscale = models.CharField(
        max_length=16,
        unique=True,
        validators=[codice_fiscale_validator]
    )
    ragione_sociale = models.CharField(
        max_length=60
    )
    indirizzo = models.CharField(
        max_length=35
    )
    citta = models.CharField(
        max_length=20
    )

# modello corrispondente a tabella Fattura
class Fattura(models.Model):
    numero = models.CharField(
        max_length=13,
        unique=True,
        primary_key=True,
        null=False,
        validators=[numero_fattura_validator]
    )
    data = models.CharField(
        max_length=10,
        validators=[stringa_data_validator]
    )
    imponibile = models.DecimalField(
        max_digits=7,
        decimal_places=2
    )
    iva = models.DecimalField(
        max_digits=6,
        decimal_places=2
    )
    totale = models.DecimalField(
        max_digits=7,
        decimal_places=2
    )

# modello corrispondente a tabella Utenza
class Utenza(models.Model):
    codice = models.CharField(
        max_length=8,
        unique=True,
        primary_key=True,
        null=False,
        validators=[codice_numerico_validator]
    )
    cliente = models.ForeignKey(
        Cliente,
        on_delete=models.PROTECT
    )
    data_apertura = models.CharField(
        max_length=10,
        validators = [stringa_data_validator]
    )
    indirizzo = models.CharField(
        max_length=35
    )
    citta = models.CharField(
        max_length=20
    )
    stato = models.CharField(
        max_length=8
    )
    data_chiusura = models.CharField(
        max_length=10,
        blank=True,
        null=True,
        validators = [stringa_data_validator]
    )
    
# modello corrispondente a tabella Lettura
class Lettura(models.Model):
    numero = models.CharField(
        max_length=8,
        unique=True,
        validators=[codice_numerico_validator]
    )
    utenza = models.ForeignKey(
        Utenza,
        on_delete=models.PROTECT
    )
    fattura = models.ForeignKey(
        Fattura,
        on_delete=models.PROTECT,
        null=True,
        blank=True
    )
    data = models.CharField(
        max_length=10,
        validators = [stringa_data_validator]
    )
    valore = models.PositiveIntegerField()

    class Meta:
        #numero e utenza di lettura e' unica nel DB
        constraints = [models.UniqueConstraint(fields=['numero', 'utenza'], name='unique_lettura_utenza')]
