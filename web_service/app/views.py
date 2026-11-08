from django.shortcuts import render
from rest_framework.decorators import api_view
from rest_framework.response import Response
from .serializers import ClienteSerializer, FatturaSerializer, LetturaSerializer, UtenzaSerializer

# Create your views here.

def inserisci(request, serializer_class, model_class):
    try:
        serializer = serializer_class(data=request.data, many=True)

        if not serializer.is_valid():
            return Response({
                'codice_errore': 'DATI_NON_VALIDI',
                'dettagli': serializer.errors
            }, status=400)

        istanze = [model_class(**dato) for dato in serializer.validated_data]
        model_class.objects.bulk_create(istanze)

        return Response({
            'esito': 'ok',
            'righe_inserite': len(istanze)
        }, status=201)

    except IntegrityError:
        return Response({
            'codice_errore': 'VIOLAZIONE_VINCOLO',
            'dettagli': 'Dato duplicato o vincolo di unicità violato'
        }, status=409)

    except OperationalError:
        return Response({
            'codice_errore': 'DATABASE_NON_RAGGIUNGIBILE',
            'dettagli': 'Impossibile connettersi al database locale'
        }, status=503)

    except Exception as e:
        return Response({
            'codice_errore': 'ERRORE_IMPREVISTO',
            'dettagli': str(e)
        }, status=500)


@api_view(['POST'])
def inserisci_clienti(request):
    return inserisci(request, ClienteSerializer, Cliente)

@api_view(['POST'])
def inserisci_fatture(request):
    return inserisci(request, FatturaSerializer, Fattura)

@api_view(['POST'])
def inserisci_prodotti(request):
    return inserisci(request, LetturaSerializer, Lettura)

@api_view(['POST'])
def inserisci_utenze(request):
    return inserisci(request, UtenzaSerializer, Utenza)

