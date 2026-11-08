from rest_framework import serializers
from .models import Cliente, Fattura, Lettura, Utenza

class ClienteSerializer(serializers.ModelSerializer):
    class Meta:
        model = Cliente         # riferimento al modello Cliente
        fields = '__all__'      # comprende tutti i campi del modello

class FatturaSerializer(serializers.ModelSerializer):
    class Meta:
        model = Fattura         # riferimento al modello Fattura
        fields = '__all__'      # comprende tutti i campi del modello

class LetturaSerializer(serializers.ModelSerializer):
    class Meta:
        model = Lettura         # riferimento al modello Lettura
        fields = '__all__'      # comprende tutti i campi del modello

class UtenzaSerializer(serializers.ModelSerializer):
    class Meta:
        model = Utenza          # riferimento al modello Utenza
        fields = '__all__'      # comprende tutti i campi del modello
